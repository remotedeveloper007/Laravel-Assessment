<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Events\UserOnlineStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    //
    public function showLogin()
    {
        return view('customer.auth.login');
    }

    public function showRegister()
    {
        return view('customer.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:customers,email',
            'password'=>'required|string|min:6|confirmed',
        ]);
        $data['password'] = Hash::make($data['password']);
        $user = Customer::create($data);
        Auth::guard('customer')->login($user);

        return redirect()->route('customer.dashboard')->with([
            'status' => 'Welcome, Customer!',
            'alert-type' => 'success'
        ]);        
    }

    public function login(Request $request)
    {
        $cred = $request->validate(['email'=>'required|email','password'=>'required']);
        if (Auth::guard('customer')->attempt($cred, $request->boolean('remember'))) {
            $request->session()->regenerate();

        
            $user = Auth::guard('customer')->user();

            event(new UserOnlineStatus($user));    
            //
            return redirect()->route('customer.dashboard')->with([
                'status' => 'Welcome, back!',
                'alert-type' => 'success'
            ]);            
        }
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('customer')->user();

        event(new UserOnlineStatus($user));

        $user->forceFill([
            'online' => false,
            'last_seen_at' => now(),
        ])->saveQuietly(); 

        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login')->with(['status' => 'Logout successfully!']);;
    }    
}
