<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            AuditLog::record('admin_login', 'auth', (string) Auth::id(), [
                'status' => 'success',
                'email' => $request->input('email'),
            ]);

            return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome back to the PGIA Knowledge Management Console.');
        }

        AuditLog::create([
            'user_id' => null,
            'user_email' => $request->input('email'),
            'action' => 'failed_login_attempt',
            'target_type' => 'auth',
            'details' => ['attempted_email' => $request->input('email')],
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 500),
            'created_at' => now(),
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our institutional records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();
        $email = Auth::user()?->email;

        AuditLog::record('admin_logout', 'auth', (string) $userId, [
            'email' => $email,
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been securely logged out.');
    }
}
