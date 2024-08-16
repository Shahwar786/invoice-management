<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected function redirectTo()
    {
        $role = Auth::user()->role->name;

        switch ($role) {
            case 'Admin':
                return '/admin/dashboard';
            case 'Camp':
                return '/camp/dashboard';
            case 'Sales Supervisor':
                return '/sales-supervisor/dashboard';
            case 'Accounts':
                return '/accounts/dashboard';
            case 'Staff':
                return '/staff/dashboard';
            case 'Kitchen':
                return '/kitchen/dashboard';
            default:
                return '/home';
        }
    }
}
