<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::check()) {
            $role = Auth::user()->role->name;

            // Redirect users based on their role
            switch ($role) {
                case 'Admin':
                    return redirect('/admin/dashboard');
                case 'Camp':
                    return redirect('/camp/dashboard');
                case 'Sales Supervisor':
                    return redirect('/sales-supervisor/dashboard');
                case 'Accounts':
                    return redirect('/accounts/dashboard');
                case 'Staff':
                    return redirect('/staff/dashboard');
                case 'Kitchen':
                    return redirect('/kitchen/dashboard');
                default:
                    return redirect('/home');
            }
        }

        return $next($request);
    }
}
