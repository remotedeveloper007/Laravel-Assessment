<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $qty = $this->faker->numberBetween(1, 5);
        $price = $this->faker->randomFloat(2, 10, 200);

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'price' => $price,
            'qty' => $qty,
            'subtotal' => $price * $qty,
        ];
    }
}
