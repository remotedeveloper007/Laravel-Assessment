<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product()
    {
        $admin = Admin::factory()->create(['password'=>bcrypt('secret')]);
        $this->actingAs($admin, 'admin');

        $resp = $this->post(route('products.store'), [
            'name'=>'Test Product',
            'price'=>99.99,
            'stock'=>10,
            'category'=>'Cat',
            'description'=>'Desc',
        ]);

        $resp->assertRedirect();
        $this->assertDatabaseHas('products', ['name'=>'Test Product','stock'=>10]);
    }
}
