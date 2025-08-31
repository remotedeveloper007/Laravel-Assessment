<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Events\UserOnlineStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    //
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function showRegister()
    {
        return view('admin.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:admins,email',
            'password'=>'required|string|min:6|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);
        $admin = Admin::create($data);
        Auth::guard('admin')->login($admin);

        return redirect()->route('admin.dashboard')->with([
            'status' => 'Welcome, admin!',
            'alert-type' => 'success'
        ]);
    }

    public function login(Request $request)
    {
        $cred = $request->validate(['email'=>'required|email','password'=>'required']);
        if (Auth::guard('admin')->attempt($cred, $request->boolean('remember'))) {
            $request->session()->regenerate();

            event(new \App\Events\UserOnlineStatus(Auth::guard('admin')->user()));
            
            return redirect()->route('admin.dashboard')->with([
                'status' => 'Welcome, admin!',
                'alert-type' => 'success'
            ]);            
        }
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('admin')->user();

        event(new \App\Events\UserOnlineStatus(Auth::guard('admin')->user()));

        $user->update([
            'online' => false,
            'last_seen_at' => now(),
        ]); 

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')->with(['status' => 'Admin logout!']);
    }  
}
