<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $credentials = $request->only('email', 'password');

        $customer = Customer::where('email', $credentials['email'])->first();

        if ($customer && $credentials['password'] === $customer->password) {
            session([
                'user' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                ],
                'isLoggedIn' => true,
                'customer_id' => $customer->id,
                'customer_address' => $customer->address ?? '',
            ]);
            return redirect('/produk');
        } else {
            return back()->withErrors(['email' => 'Email atau password salah.']);
        }
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
