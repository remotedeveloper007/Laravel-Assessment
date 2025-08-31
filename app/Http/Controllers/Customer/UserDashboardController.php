<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:customer');
    }
    
    public function store(Request $request)
    {   
        //dd($request->all());
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $customer = Auth::guard('customer')->user();
        // dd($data);
        $order = DB::transaction(function () use ($data, $customer) {
            $order = Order::create([
                'customer_id' => $customer->id,
                'status' => 'Pending',
                'total' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $qty = $item['qty'];

                if ($product->stock < $qty) {
                    abort(422, "Insufficient stock for {$product->name}");
                }

                $product->decrement('stock', $qty);

                $orderItem = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'qty' => $qty,
                    'subtotal' => $product->price * $qty,
                ];

                OrderItem::create($orderItem);
                $total += $orderItem['subtotal'];
            }

            $order->update(['total' => $total]);

            return $order;
        });

        event(new OrderPlaced($order));

        return redirect()->route('customer.dashboard')->with([
            'status' => 'Order placed successfully.'
        ]);
    }   
}
