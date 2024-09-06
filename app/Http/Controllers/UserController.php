<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;  


class UserController extends Controller
{
    // Show the registration form
    public function showRegistrationForm()
    {
        return view('auth.register');  
 // Assuming you're using Laravel's built-in auth views
    }

    // Handle user registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',  

            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);  
 // Automatically log in the user after registration

        return redirect()->intended('cardgame'); // Redirect to the card game or another desired route
    }

    // Show the login form
    public function showLoginForm()
    {
        return view('auth.login'); // Assuming you're using Laravel's built-in auth views
    }

    // Handle user login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials,  
 $request->has('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('cardgame'); // Redirect to the card game or another desired route
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    // Handle user logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // Redirect to the home page or another desired route
    }
}