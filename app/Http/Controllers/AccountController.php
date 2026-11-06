<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Support\Catalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Customer accounts: sign in, register, sign out and the My Account dashboard
 * (orders, wishlist, saved address, details and password).
 *
 * The sign-in and register forms post with fetch (see authForm in alpine.js) and
 * get JSON back; plain form posts are redirected instead.
 */
class AccountController extends Controller
{
    public function login(): View
    {
        return view('frontend.account.login');
    }

    public function authenticate(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Five failed attempts per email + IP locks sign-in for a minute.
        $key = 'customer-login:'.Str::lower($credentials['email']).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Too many attempts. Please try again in '.RateLimiter::availableIn($key).' seconds.',
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages(['email' => 'These details don’t match an account. Check your email and password.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return $this->redirectTo($request, redirect()->intended(route('account'))->getTargetUrl());
    }

    public function register(): View
    {
        return view('frontend.account.register');
    }

    public function storeRegistration(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[+\d\s()-]{7,}$/'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'terms' => ['accepted'],
        ], [
            'email.unique' => 'An account with this email already exists. Try signing in instead.',
            'terms.accepted' => 'Please accept the terms to continue.',
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => Str::lower($data['email']),
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();

        return $this->redirectTo($request, redirect()->intended(route('account'))->getTargetUrl());
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function forgotPassword(): View
    {
        return view('frontend.account.forgot-password');
    }

    public function resetPassword(?string $token = null): View
    {
        return view('frontend.account.reset-password', ['token' => $token]);
    }

    public function dashboard(Request $request): View
    {
        $products = Catalog::products();

        return view('frontend.account.dashboard', [
            'user' => $request->user(),
            'orders' => $request->user()->orders()->with('items')->get(),
            'suggestions' => $products->take(3),
            // Wishlist slugs are kept in the browser; the dashboard looks them up in this list.
            'wishlistProducts' => $products->map(fn ($p) => [
                'slug' => $p['slug'], 'name' => $p['name'], 'url' => $p['url'], 'image_url' => $p['image_url'],
                'price_label' => $p['price_label'], 'category_name' => $p['category_name'],
            ])->values(),
        ]);
    }

    public function order(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return view('frontend.account.order', ['order' => $order->load('items')]);
    }

    public function updateDetails(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validateWithBag('details', [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[+\d\s()-]{7,}$/'],
        ]);

        $user->update(['name' => $data['name'], 'email' => Str::lower($data['email']), 'phone' => $data['phone'] ?? null]);

        return redirect(route('account').'#details')->with('notice', 'Your details have been saved.');
    }

    public function updateAddress(Request $request): RedirectResponse
    {
        $request->user()->update(['address' => self::validateAddress($request, 'address')]);

        return redirect(route('account').'#addresses')->with('notice', 'Your delivery address has been saved.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], ['current_password.current_password' => 'Your current password is incorrect.']);

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return redirect(route('account').'#details')->with('notice', 'Your password has been updated.');
    }

    /** Delivery address rules, shared with checkout. */
    public static function validateAddress(Request $request, string $bag = 'default'): array
    {
        return $request->validateWithBag($bag, [
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[+\d\s()-]{7,}$/'],
            'line1' => ['required', 'string', 'max:190'],
            'line2' => ['nullable', 'string', 'max:190'],
            'city' => ['required', 'string', 'max:80'],
            'state' => ['required', 'string', 'max:80'],
            'postcode' => ['required', 'regex:/^\d{6}$/'],
        ], [
            'phone.regex' => 'Please enter a valid phone number.',
            'postcode.regex' => 'Please enter a 6-digit PIN code.',
        ]);
    }

    private function redirectTo(Request $request, string $url): JsonResponse|RedirectResponse
    {
        return $request->expectsJson() ? response()->json(['redirect' => $url]) : redirect($url);
    }
}
