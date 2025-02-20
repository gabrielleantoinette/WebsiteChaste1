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
        return view('login'); // Pastikan file login.blade.php ada di resources/views
    }

    public function loginadmin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        // Cari user berdasarkan username
        $employee = Employee::where('username', $request->username)->first();

        // Cek apakah user ditemukan dan password tidak di-hash (plaintext)
        if ($employee && $employee->password === $request->password) {
            // Simpan sesi login manual
            Session::put('login_id', $employee->id);
            Session::put('role', $employee->role);

            // Redirect berdasarkan role
            switch ($employee->role) {
                case '0': return redirect('/owner/dashboard');
                case '1': return redirect('/admin/dashboard');
                case '2': return redirect('/kurir/dashboard');
                case '3': return redirect('/staf-gudang/dashboard');
                case '4': return redirect('/staf-keuangan/dashboard');
                default: return redirect('/login')->with('error', 'Role tidak dikenali.');
            }
        }

        return back()->withErrors(['username' => 'Username atau password salah.']);
    }

    public function logout(Request $request)
    {
        Session::flush(); // Hapus semua sesi
        return redirect('/login');
    }
}
