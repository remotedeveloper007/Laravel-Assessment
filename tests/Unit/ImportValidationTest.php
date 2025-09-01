<?php

namespace Tests\Unit;

use App\Jobs\ProcessProductRow;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_job_validates_rows_and_creates_products()
    {
        $rows = [
            ['name'=>'A','description'=>'d','price'=>10,'image'=>null,'category'=>'c','stock'=>3],
            ['name'=>'B','description'=>'d','price'=>20,'image'=>'','category'=>'c','stock'=>5],
        ];

        $job = new ProcessProductRow($rows);
        $job->handle();

        $this->assertDatabaseHas('products', ['name'=>'A','stock'=>3]);
        $this->assertDatabaseHas('products', ['name'=>'B','stock'=>5]);
        $this->assertEquals(2, Product::count());
    }
}
