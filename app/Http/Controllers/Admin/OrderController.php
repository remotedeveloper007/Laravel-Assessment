<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Events\OrderStatusUpdated;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $orders = Order::with('customer')->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order) {
        $data = $request->validate(['status'=>'required|in:Pending,Shipped,Delivered']);
        $order->update(['status' => $data['status']]);

        OrderStatusUpdated::dispatch($order->fresh('customer'));

        return back()->with(['status' => 'Order status updated!']);
    }
}
