<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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

    public function showRegisterForm()
    {
        return view('register');
    }

    public function loginadmin(Request $request)
    {
        $employee = Employee::where('email', $request->email)->first();
        if ($employee) {
            if ($employee->password == $request->password) {
                Session::put('user', $employee);
                return redirect('/admin');
            } else {
                return back()->with('error', 'Password salah.');
            }
        }

        //login customer
        $customer = Customer::where('email', $request->email)->first();
        if ($customer) {
            if ($customer->password == $request->password) {
                Session::put('user', $customer);
                return redirect('/');
            } else {
                return back()->with('error', 'Password salah.');
            }
        }

        return back()->with('error', 'Email tidak ditemukan.');
    }

    public function logout(Request $request)
    {
        Session::flush();
        return redirect('/login');
    }

    public function register(Request $request)
    {
        $customer = new Customer();
        $customer->email = $request->email;
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->password = $request->password;
        $customer->save();


        Session::put('user', $customer);

        return redirect('/');
    }
}
