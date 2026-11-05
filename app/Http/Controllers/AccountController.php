<?php

namespace App\Http\Controllers;

use App\Support\Catalog;
use Illuminate\View\View;

/**
 * Account screens — frontend only. Forms validate in the browser and walk
 * through the flow; nothing is authenticated or stored. Replace with Laravel
 * auth (e.g. Fortify/Breeze controllers) in the backend phase, keeping these
 * route names so existing links continue to work.
 */
class AccountController extends Controller
{
    public function login(): View
    {
        return view('frontend.account.login');
    }

    public function register(): View
    {
        return view('frontend.account.register');
    }

    public function forgotPassword(): View
    {
        return view('frontend.account.forgot-password');
    }

    public function resetPassword(?string $token = null): View
    {
        return view('frontend.account.reset-password', ['token' => $token]);
    }

    public function dashboard(): View
    {
        return view('frontend.account.dashboard', [
            'suggestions' => Catalog::products()->take(3),
        ]);
    }
}
