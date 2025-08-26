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
        $rawMaterials = \App\Models\RawMaterial::orderBy('name')->get();
        
        return view('admin.work-orders.create', compact('gudangEmployees', 'rawMaterials'));
    }

    /**
     * Test method untuk debugging
     */
    public function testStore(Request $request)
    {
        \Log::info('Test store method called');
        \Log::info('Request data:', $request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Test method berhasil dipanggil',
            'data' => $request->all()
        ]);
    }

    /**
     * Menyimpan surat perintah kerja baru
     */
    public function store(Request $request)
    {
        // Debug: Log request data
        \Log::info('Work Order Store Request:', $request->all());
        
        // Debug: Check if items exist
        if (!$request->has('items') || empty($request->items)) {
            \Log::error('No items found in request');
            return back()->with('error', 'Minimal harus ada 1 item dalam surat perintah kerja.')->withInput();
        }
        
        // Debug: Log each item
        foreach ($request->items as $index => $item) {
            \Log::info("Item {$index}:", $item);
        }
        
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
                'items.*.raw_material_id' => 'required|exists:raw_materials,id',
                'items.*.size' => 'required|string',
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
            
            // Validate user session
            if (!$user) {
                throw new \Exception('User session tidak valid');
            }
            
            // Check if assigned_to exists
            $assignedEmployee = Employee::find($request->assigned_to);
            if (!$assignedEmployee) {
                throw new \Exception('Staff gudang tidak ditemukan');
            }
            
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
                
                // Ambil data bahan baku
                $rawMaterial = \App\Models\RawMaterial::find($item['raw_material_id']);
                if (!$rawMaterial) {
                    throw new \Exception('Bahan baku dengan ID ' . $item['raw_material_id'] . ' tidak ditemukan');
                }
                
                $itemData = [
                    'work_order_id' => $workOrder->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'size_material' => $rawMaterial->name . ' ' . $item['size'], // Gabungkan nama bahan + ukuran
                    'color' => $item['color'],
                    'quantity' => $item['quantity'],
                    'remarks' => $item['remarks'] ?? null,
                    'status' => 'pending'
                ];
                
                try {
                    $workOrderItem = WorkOrderItem::create($itemData);
                    \Log::info('Item created:', ['id' => $workOrderItem->id]);
                } catch (\Exception $e) {
                    throw new \Exception('Gagal membuat item ' . ($index + 1) . ': ' . $e->getMessage());
                }
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
            
            // Debug: Log success
            \Log::info('Work order created successfully with ID: ' . $workOrder->id);
            
            // For debugging, return JSON response first
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat perintah kerja berhasil dibuat',
                    'work_order_id' => $workOrder->id,
                    'code' => $workOrder->code
                ]);
            }
            
            // Try redirect with error handling
            try {
                return redirect('/admin/work-orders')->with('success', 'Surat perintah kerja berhasil dibuat dengan kode: ' . $workOrder->code);
            } catch (\Exception $e) {
                \Log::error('Redirect error:', ['message' => $e->getMessage()]);
                return back()->with('success', 'Surat perintah kerja berhasil dibuat dengan kode: ' . $workOrder->code);
            }

        } catch (\Exception $e) {
            \Log::error('Error in work order creation:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            DB::rollback();
            
            // Return with more specific error message
            $errorMessage = 'Terjadi kesalahan saat membuat surat perintah kerja: ' . $e->getMessage();
            return back()->with('error', $errorMessage)->withInput();
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
            
            // Update stok bahan baku dan produk ketika work order selesai
            $this->updateStockOnWorkOrderComplete($workOrder);
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
            
            // Update stok bahan baku dan produk ketika work order selesai
            $this->updateStockOnWorkOrderComplete($workOrder);
        }

        // Kirim notifikasi ke admin
        $notificationService = app(NotificationService::class);
        $notificationService->notifyWorkOrderStatusUpdate($workOrder->id, [
            'code' => $workOrder->code,
            'status' => $request->status
        ]);

        return back()->with('success', 'Status surat perintah kerja berhasil diupdate.');
    }

    /**
     * Update stok bahan baku dan produk ketika work order selesai
     */
    private function updateStockOnWorkOrderComplete($workOrder)
    {
        DB::beginTransaction();
        try {
            \Log::info('Updating stock for work order: ' . $workOrder->id);
            
            foreach ($workOrder->items as $item) {
                if ($item->status === 'completed' && $item->completed_quantity > 0) {
                    \Log::info('Processing item: ' . $item->id . ' with completed_quantity: ' . $item->completed_quantity);
                    
                    // Ambil data bahan baku dari raw_material_id
                    $rawMaterial = \App\Models\RawMaterial::find($item->raw_material_id);
                    if (!$rawMaterial) {
                        \Log::error('Raw material not found for item: ' . $item->id);
                        continue;
                    }
                    
                    // Hitung kebutuhan bahan baku (dalam meter persegi)
                    $kebutuhanBahanBaku = $item->completed_quantity * $this->calculateMaterialNeeded($item->size_material);
                    \Log::info('Kebutuhan bahan baku: ' . $kebutuhanBahanBaku . ' m²');
                    
                    // Update stok bahan baku (kurangi)
                    $oldStock = $rawMaterial->stock;
                    $rawMaterial->stock = max(0, $rawMaterial->stock - $kebutuhanBahanBaku);
                    $rawMaterial->save();
                    
                    \Log::info('Raw material stock updated: ' . $rawMaterial->name . ' from ' . $oldStock . ' to ' . $rawMaterial->stock);
                    
                    // Update stok produk (tambah) - jika ada produk yang sesuai
                    $this->updateProductStock($item->size_material, $item->color, $item->completed_quantity, 'increase');
                }
            }
            
            DB::commit();
            \Log::info('Stock update completed successfully');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating stock on work order complete: ' . $e->getMessage());
        }
    }

    /**
     * Hitung kebutuhan bahan baku berdasarkan ukuran
     */
    private function calculateMaterialNeeded($sizeMaterial)
    {
        // Parse ukuran dari format "Terpal A8 2x3" atau "2x3"
        if (preg_match('/(\d+)x(\d+)/', $sizeMaterial, $matches)) {
            $panjang = (int)$matches[1];
            $lebar = (int)$matches[2];
            $luas = $panjang * $lebar;
            \Log::info('Calculated material needed: ' . $panjang . 'x' . $lebar . ' = ' . $luas . ' m²');
            return $luas;
        }
        \Log::warning('Could not parse size from: ' . $sizeMaterial);
        return 0;
    }



    /**
     * Update stok produk
     */
    private function updateProductStock($sizeMaterial, $color, $quantity, $action)
    {
        // Cari produk berdasarkan ukuran dan warna
        $product = \App\Models\Product::where('size', $sizeMaterial)->first();
        if ($product) {
            $variant = $product->variants()->where('color', $color)->first();
            if ($variant) {
                if ($action === 'increase') {
                    $variant->stock += $quantity;
                } else {
                    $variant->stock = max(0, $variant->stock - $quantity);
                }
                $variant->save();
            }
        }
    }
}
