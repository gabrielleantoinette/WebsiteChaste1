<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function loginadmin(Request $request)
    {
        $employee = Employee::where('email', $request->email)->first();
        if ($employee) {
            if ($employee->password == $request->password) {
                Session::put('user', $employee);
                return redirect('/admin');
            } else {
                return back()->withErrors(['password' => 'Password salah.']);
            }
        }
        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    public function logout(Request $request)
    {
        Session::flush();
        return redirect('/login');
    }
}
