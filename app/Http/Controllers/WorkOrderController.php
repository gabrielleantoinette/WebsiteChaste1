<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class WorkOrderController extends Controller
{
    // ==================== ADMIN FUNCTIONS ====================
    
    /**
     * Menampilkan daftar surat perintah kerja untuk admin
     */
    public function index()
    {
        $user = Session::get('user');
        if (!$user || !in_array($user->role, ['admin', 'owner', 'gudang'])) {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }

        // Jika role gudang, hanya tampilkan work orders yang ditugaskan ke user tersebut
        if ($user->role === 'gudang') {
            $workOrders = WorkOrder::with(['createdBy', 'assignedTo', 'items'])
                ->where('assigned_to', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Jika admin/owner, tampilkan semua work orders
            $workOrders = WorkOrder::with(['createdBy', 'assignedTo', 'items'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('admin.work-orders.index', compact('workOrders'));
    }

    /**
     * Menampilkan form pembuatan surat perintah kerja
     */
    public function create()
    {
        $user = Session::get('user');
        if (!$user || !in_array($user->role, ['admin', 'owner'])) {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }

        $gudangEmployees = Employee::where('role', 'gudang')->get();
        
        return view('admin.work-orders.create', compact('gudangEmployees'));
    }

    /**
     * Menyimpan surat perintah kerja baru
     */
    public function store(Request $request)
    {
        // Debug: Log request data
        \Log::info('Work Order Store Request:', $request->all());
        
        $user = Session::get('user');
        if (!$user || !in_array($user->role, ['admin', 'owner'])) {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }

        try {
            $request->validate([
                'order_date' => 'required|date',
                'due_date' => 'nullable|date|after_or_equal:order_date',
                'description' => 'nullable|string',
                'assigned_to' => 'required|exists:employees,id',
                'items' => 'required|array|min:1',
                'items.*.size_material' => 'required|string',
                'items.*.color' => 'required|string',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.remarks' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Error:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();
        try {
            \Log::info('Starting database transaction...');
            
            // Generate kode surat perintah
            $lastOrder = WorkOrder::orderBy('id', 'desc')->first();
            $lastNumber = $lastOrder ? intval(substr($lastOrder->code, 3)) : 0;
            $newNumber = $lastNumber + 1;
            $code = 'SP-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            \Log::info('Generated code:', ['code' => $code]);

            // Buat work order
            $workOrderData = [
                'code' => $code,
                'order_date' => $request->order_date,
                'due_date' => $request->due_date,
                'description' => $request->description,
                'status' => 'dibuat',
                'created_by' => $user->id,
                'assigned_to' => $request->assigned_to,
                'notes' => $request->notes
            ];
            \Log::info('Creating work order with data:', $workOrderData);
            
            $workOrder = WorkOrder::create($workOrderData);
            \Log::info('Work order created:', ['id' => $workOrder->id]);

            // Buat items
            \Log::info('Creating work order items...');
            foreach ($request->items as $index => $item) {
                \Log::info('Creating item ' . ($index + 1) . ':', $item);
                $itemData = [
                    'work_order_id' => $workOrder->id,
                    'size_material' => $item['size_material'],
                    'color' => $item['color'],
                    'quantity' => $item['quantity'],
                    'remarks' => $item['remarks'] ?? null,
                    'status' => 'pending'
                ];
                $workOrderItem = WorkOrderItem::create($itemData);
                \Log::info('Item created:', ['id' => $workOrderItem->id]);
            }

            // Kirim notifikasi ke gudang
            \Log::info('Sending notification...');
            $notificationService = app(NotificationService::class);
            $notificationService->notifyWorkOrderNew($workOrder->id, [
                'code' => $workOrder->code,
                'assigned_to' => $request->assigned_to
            ]);
            \Log::info('Notification sent successfully');

            DB::commit();
            \Log::info('Database transaction committed successfully');
            return redirect()->route('admin.work-orders.index')->with('success', 'Surat perintah kerja berhasil dibuat.');

        } catch (\Exception $e) {
            \Log::error('Error in work order creation:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail surat perintah kerja
     */
    public function show($id)
    {
        $user = Session::get('user');
        if (!$user) {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }

        $workOrder = WorkOrder::with(['createdBy', 'assignedTo', 'items'])->findOrFail($id);

        // Cek akses
        if (!in_array($user->role, ['admin', 'owner']) && $workOrder->assigned_to != $user->id) {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }

        return view('admin.work-orders.show', compact('workOrder'));
    }

    /**
     * Menampilkan form edit surat perintah kerja
     */
    public function edit($id)
    {
        $user = Session::get('user');
        if (!$user || !in_array($user->role, ['admin', 'owner'])) {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }

        $workOrder = WorkOrder::with(['items'])->findOrFail($id);
        $gudangEmployees = Employee::where('role', 'gudang')->get();

        return view('admin.work-orders.edit', compact('workOrder', 'gudangEmployees'));
    }

    /**
     * Update surat perintah kerja
     */
    public function update(Request $request, $id)
    {
        $user = Session::get('user');
        if (!$user || !in_array($user->role, ['admin', 'owner'])) {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }

        $workOrder = WorkOrder::findOrFail($id);

        $request->validate([
            'order_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:order_date',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:employees,id',
            'status' => 'required|in:dibuat,dikerjakan,selesai,dibatalkan',
            'notes' => 'nullable|string',
        ]);

        $workOrder->update([
            'order_date' => $request->order_date,
            'due_date' => $request->due_date,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->route('admin.work-orders.show', $id)->with('success', 'Surat perintah kerja berhasil diupdate.');
    }

    // ==================== GUDANG FUNCTIONS ====================

    /**
     * Update status item work order
     */
    public function updateItemStatus(Request $request, $workOrderId, $itemId)
    {
        $user = Session::get('user');
        if (!$user || $user->role !== 'gudang') {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }

        $workOrder = WorkOrder::where('assigned_to', $user->id)->findOrFail($workOrderId);
        $item = WorkOrderItem::where('work_order_id', $workOrderId)->findOrFail($itemId);

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
            'completed_quantity' => 'required|integer|min:0|max:' . $item->quantity,
            'notes' => 'nullable|string'
        ]);

        $item->update([
            'status' => $request->status,
            'completed_quantity' => $request->completed_quantity,
            'notes' => $request->notes
        ]);

        // Update status work order jika semua item selesai
        $allCompleted = $workOrder->items()->where('status', '!=', 'completed')->count() == 0;
        if ($allCompleted && $workOrder->status !== 'selesai') {
            $workOrder->update([
                'status' => 'selesai',
                'completed_at' => now()
            ]);
        } elseif ($request->status === 'in_progress' && $workOrder->status === 'dibuat') {
            $workOrder->update([
                'status' => 'dikerjakan',
                'started_at' => now()
            ]);
        }

        return back()->with('success', 'Status item berhasil diupdate.');
    }

    /**
     * Update status work order
     */
    public function updateWorkOrderStatus(Request $request, $id)
    {
        $user = Session::get('user');
        if (!$user || $user->role !== 'gudang') {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }

        $workOrder = WorkOrder::where('assigned_to', $user->id)->findOrFail($id);

        $request->validate([
            'status' => 'required|in:dibuat,dikerjakan,selesai,dibatalkan',
            'notes' => 'nullable|string'
        ]);

        $workOrder->update([
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        if ($request->status === 'dikerjakan' && !$workOrder->started_at) {
            $workOrder->update(['started_at' => now()]);
        } elseif ($request->status === 'selesai') {
            $workOrder->update(['completed_at' => now()]);
        }

        // Kirim notifikasi ke admin
        $notificationService = app(NotificationService::class);
        $notificationService->notifyWorkOrderStatusUpdate($workOrder->id, [
            'code' => $workOrder->code,
            'status' => $request->status
        ]);

        return back()->with('success', 'Status surat perintah kerja berhasil diupdate.');
    }
}
