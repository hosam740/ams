<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\UserRegisterationRequest;
use App\Http\Requests\Authentication\UserLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    //

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    
    public function register(UserRegisterationRequest $request)
    {
        $data = $request->validated();

        // Password will be hashed automatically (because of the "hashed" cast in User model)
        $user = User::create($data);

        // Log the user in right after registration
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect to home (no flash messages)
        return redirect()->intended('/');
    }

    public function login(UserLoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            // Prevent session fixation
            $request->session()->regenerate();
            return redirect()->intended('/'); // No success message
        }

        // Return back with validation error (not a success message)
        return back()->withErrors([
            'email' => 'بيانات تسجيل الدخول غير صحيحة.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate and regenerate session for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to login page (no flash messages)
        return redirect()->route('show.login');
    }
}
