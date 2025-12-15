<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('user')) {
            return redirect('/login');
        }

        $user = Session::get('user');
        $role = is_array($user) ? ($user['role'] ?? '') : ($user->role ?? '');

        // Roles that are allowed to access the admin area
        $allowedRoles = ['admin', 'owner', 'gudang', 'driver', 'keuangan'];

        if (!in_array($role, $allowedRoles, true)) {
            return redirect('/')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}

