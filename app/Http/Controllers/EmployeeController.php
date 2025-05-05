<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

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
        $employee->password = $password;
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
        return view('admin.employees.detail', [
            'employee' => Employee::find($id)
        ]);
    }

    public function updateEmployeeAction(Request $request, $id)
    {
        $employee = Employee::find($id);

        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->password = $request->password;
        $employee->role = $request->role;
        $employee->active = $request->active === "true" ? true : false;

        $employee->phone = $request->phone;
        $employee->ktp = $request->ktp;

        // Ganti ke profile_picture (bukan photo)
        if ($request->hasFile('profile_picture')) {
            $profile_picture = $request->file('profile_picture')->store('photos', 'public');
            $employee->profile_picture = basename($profile_picture);
        }

        $employee->save();

        return redirect('/admin/employees/');
    }
}
