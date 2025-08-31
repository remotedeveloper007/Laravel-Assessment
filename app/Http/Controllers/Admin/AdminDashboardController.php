<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\Product;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    //
    public function dashboard() {
        //
        $adminsOnline = Admin::where('online',true)->count();
        $customersOnline = Customer::where('online',true)->count();
        $productsCount = Product::count();

        return view('admin.dashboard', compact('adminsOnline','customersOnline', 'productsCount'));             
    }
}
