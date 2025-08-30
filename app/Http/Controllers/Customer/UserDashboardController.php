<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    //
    public function dashboard()
    {
        return redirect()->route('shop.index');
    }    
}
