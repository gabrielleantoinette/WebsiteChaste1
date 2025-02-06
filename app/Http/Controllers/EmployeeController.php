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

    public function createAction(Request $request)
    {
        $name = $request->input('name');
        $username = $request->input('username');
        $password = $request->input('password');
        $active = $request->input('active') === "1" ? true : false;
        $role = $request->input('role');

        $employee = new Employee();
        $employee->name = $name;
        $employee->username = $username;
        $employee->password = $password;
        $employee->active = $active;
        $employee->role = $role;
        $employee->save();

        return redirect('/admin/employees');
    }

    public function detail($id)
    {
        return view('admin.employees.detail', [
            'product' => Employee::find($id)
        ]);
    }
}
