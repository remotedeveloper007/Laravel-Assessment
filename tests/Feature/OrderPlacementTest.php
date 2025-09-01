<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


use Illuminate\Foundation\Testing\WithFaker;


class OrderPlacementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_place_order()
    {
        $customer = Customer::factory()->create();
        $product  = Product::factory()->create(['stock' => 5, 'price' => 100]);

        $response = $this->actingAs($customer, 'customer')->post(route('customer.orders.store'), [
            'items' => [
                ['product_id' => $product->id, 'qty' => 2],
            ]
        ]);

        $response->assertRedirect(route('customer.dashboard'));

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'status' => 'Pending',
            'total' => 200,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'qty' => 2,
            'subtotal' => 200,
        ]);

        $this->assertEquals(3, $product->fresh()->stock); // stock reduced
    }
}
