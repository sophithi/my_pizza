<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Attempt to authenticate
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // Record login activity
            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'activity_at' => now(),
            ]);

            // Update user's last login info
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
                'last_login_user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Invalid email or password']);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Record logout activity
            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'logout',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'activity_at' => now(),
            ]);

            // Update user's last logout info
            $user->update([
                'last_logout_at' => now(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show user activity/session history
     */
    public function activityLog()
    {
        $user = Auth::user();
        $activities = $user->activities()->latest()->paginate(20);

        return view('auth.activity-log', compact('activities', 'user'));
    }

    /**
     * Get user's current session info
     */
    public function sessionInfo()
    {
        $user = Auth::user();

        return response()->json([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'last_login_at' => $user->last_login_at?->setTimezone('Asia/Phnom_Penh')->format('Y-m-d H:i:s'),
            'last_login_ip' => $user->last_login_ip,
            'current_ip' => request()->ip(),
            'current_user_agent' => request()->userAgent(),
        ]);
    }

}
