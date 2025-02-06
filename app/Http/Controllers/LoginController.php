<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    //

    public function index()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        return redirect('/admin/dashboard');
    }
}
