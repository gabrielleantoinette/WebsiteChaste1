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
                if ($employee->role === 'gudang') {
                    return redirect('/admin/dashboard-gudang');
                }
                if ($employee->role === 'driver') {
                    return redirect('/admin/dashboard-driver');
                }
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
                    'role' => 'customer',
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
        $request->validate([
            'email' => 'required|email|unique:customers,email',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'gender' => 'nullable|in:male,female',
        ]);

        $customer = new Customer();
        $customer->email = $request->email;
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->password = $request->password;
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->province = $request->province;
        $customer->postal_code = $request->postal_code;
        $customer->gender = $request->gender;
        $customer->save();


        Session::put('user', [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'role' => 'customer',
        ]);

        return redirect('/');
    }
}
