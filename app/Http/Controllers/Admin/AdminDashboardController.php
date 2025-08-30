<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Customer;

class AdminDashboardController extends Controller
{
    //
    public function dashboard() {
        //
        $adminsOnline = Admin::where('online',true)->count();
        $customersOnline = Customer::where('online',true)->count();

        return view('admin.dashboard', compact('adminsOnline','customersOnline'));             
    }
}
