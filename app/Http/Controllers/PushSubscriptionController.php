<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:customer');
    }

    public function store(Request $request)
    {
        $request->validate([
            'endpoint'=>'required|url',
            'keys.p256dh'=>'required',
            'keys.auth'=>'required',
        ]);

        $user = Auth::guard('customer')->user();

        PushSubscription::updateOrCreate([
            'customer_id' => $user->id,
            'endpoint' => $request->endpoint
        ], [
            'p256dh' => $request->input('keys.p256dh'),
            'auth' => $request->input('keys.auth')
        ]);

        return response()->json(['success' => true]);
    }    
}
