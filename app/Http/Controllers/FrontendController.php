<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class FrontendController extends Controller
{
    //
    public function index(Request $request)
    {
        //
        $query = Product::query();
        if ($search = $request->get('search')) {
            $query->where('name','like',"$search%")->orWhere('category','like',"$search%");
        }
        
        $products = $query->orderBy('name')->paginate(20);

        return view('frontend.index', compact('products'));
    }    
}
