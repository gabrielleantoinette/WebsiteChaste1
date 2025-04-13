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

        $employee = new Employee();
        $employee->name = $name;
        $employee->email = $email;
        $employee->password = $password;
        $employee->active = $active;
        $employee->role = $role;
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
        $employee->save();
        return redirect('/admin/employees/');
    }
}
