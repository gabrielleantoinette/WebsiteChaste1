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
            // Check if password is hashed or plain text (for backward compatibility)
            $passwordMatch = false;
            if (Hash::needsRehash($employee->password) || strlen($employee->password) < 60) {
                // Plain text password (old data)
                $passwordMatch = ($employee->password == $request->password);
            } else {
                // Hashed password
                $passwordMatch = Hash::check($request->password, $employee->password);
            }
            
            if ($passwordMatch) {
                Session::put('user', $employee);
                if ($employee->role === 'gudang') {
                    return redirect('/admin/dashboard-gudang');
                }
                if ($employee->role === 'driver') {
                    return redirect('/admin/dashboard-driver');
                }
                return redirect('/admin');
            } else {
                return back()->with('error', 'Password yang Anda masukkan salah. Silakan coba lagi.');
            }
        }

        //login customer
        $credentials = $request->only('email', 'password');

        $customer = Customer::where('email', $credentials['email'])->first();

        if (!$customer) {
            return back()->withErrors(['email' => 'Email tidak terdaftar. Silakan periksa kembali email Anda atau daftar akun baru.'])->withInput($request->only('email'));
        }

        // Check if password is hashed or plain text (for backward compatibility)
        $passwordMatch = false;
        if (Hash::needsRehash($customer->password) || strlen($customer->password) < 60) {
            // Plain text password (old data)
            $passwordMatch = ($credentials['password'] === $customer->password);
            // Auto-upgrade to hashed password
            if ($passwordMatch) {
                $customer->password = Hash::make($credentials['password']);
                $customer->save();
            }
        } else {
            // Hashed password
            $passwordMatch = Hash::check($credentials['password'], $customer->password);
        }

        if ($passwordMatch) {
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
            return back()->withErrors(['password' => 'Password yang Anda masukkan salah. Silakan coba lagi.'])->withInput($request->only('email'));
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
        $customer->password = Hash::make($request->password);
        $customer->address = $request->address;
        $customer->city = $request->city;
        $customer->province = $request->province;
        $customer->postal_code = $request->postal_code;
        $customer->gender = $request->gender;
        $customer->save();

        // Set session sama seperti saat login customer
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

        return redirect('/');
    }
}
