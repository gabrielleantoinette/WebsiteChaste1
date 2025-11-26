<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function view()
    {
        return view('admin.employees.view', [
            'products' => Employee::all()
        ]);
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function createEmployeeAction(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $active = $request->input('active') === "1" ? true : false;
        $role = $request->input('role');
        $phone = $request->input('phone');
        $ktp = $request->input('ktp');
        $car_plate = $request->input('car_plate');
        $car_type = $request->input('car_type');

        $employee = new Employee();
        $employee->name = $name;
        $employee->email = $email;
        $employee->password = Hash::make($password);
        $employee->active = $active;
        $employee->role = $role;
        $employee->phone = $phone;
        $employee->ktp = $ktp;
        $employee->car_plate = $car_plate;
        $employee->car_type = $car_type;
        $employee->active = $active;
        $employee->save();

        return redirect('/admin/employees');
    }

    public function detail($id)
    {
        $employee = Employee::findOrFail($id);
        return view('admin.employees.detail', [
            'employee' => $employee
        ]);
    }

    public function updateEmployeeAction(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $employee->name = $request->name;
        $employee->email = $request->email;
        if ($request->filled('password')) {
            $employee->password = Hash::make($request->password);
        }
        $employee->role = $request->role;
        $employee->active = $request->active === "true" ? true : false;

        $employee->phone = $request->phone;
        $employee->ktp = $request->ktp;

        // Ganti ke profile_picture (bukan photo)
        if ($request->hasFile('profile_picture')) {
            // Hapus foto lama jika ada
            if ($employee->profile_picture && Storage::disk('public')->exists('photos/' . $employee->profile_picture)) {
                Storage::disk('public')->delete('photos/' . $employee->profile_picture);
            }
            $profile_picture = $request->file('profile_picture')->store('photos', 'public');
            $employee->profile_picture = basename($profile_picture);
        }

        $employee->save();

        return redirect('/admin/employees/');
    }

    public function profile()
    {
        $user = Session::get('user');
        if (!$user) {
            return redirect('/login');
        }

        // Jika user adalah array (customer), redirect
        if (is_array($user)) {
            return redirect('/login');
        }

        // Pastikan user adalah Employee
        $employee = Employee::findOrFail($user->id);
        return view('admin.employees.profile', [
            'employee' => $employee
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Session::get('user');
        if (!$user) {
            return redirect('/login');
        }

        // Jika user adalah array (customer), redirect
        if (is_array($user)) {
            return redirect('/login');
        }

        $employee = Employee::findOrFail($user->id);

        // Validasi
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'ktp' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $employee->name = $request->name;
        $employee->email = $request->email;
        
        if ($request->filled('password')) {
            $employee->password = Hash::make($request->password);
        }

        $employee->phone = $request->phone;
        $employee->ktp = $request->ktp;

        // Update profile picture
        if ($request->hasFile('profile_picture')) {
            // Hapus foto lama jika ada
            if ($employee->profile_picture && Storage::disk('public')->exists('photos/' . $employee->profile_picture)) {
                Storage::disk('public')->delete('photos/' . $employee->profile_picture);
            }
            $profile_picture = $request->file('profile_picture')->store('photos', 'public');
            $employee->profile_picture = basename($profile_picture);
        }

        $employee->save();

        // Update session dengan data terbaru
        Session::put('user', $employee);

        return redirect()->route('employee.profile')->with('success', 'Profil berhasil diperbarui');
    }
}
