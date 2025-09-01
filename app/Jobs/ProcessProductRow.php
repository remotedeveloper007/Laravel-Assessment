<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessProductRow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, Batchable, SerializesModels;

    public $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $chunkSize = 500;
        $buffer = [];

        foreach ($this->rows as $row) {
            try {
                if (!isset($row['name'], $row['price'], $row['stock'])) {
                    throw new \Exception('Missing required fields.');
                }

                $row['price'] = (float)$row['price'];
                $row['stock'] = (int)$row['stock'];
                $row['description'] = $row['description'] ?? null;
                $row['image'] = $row['image'] ?? null;
                $row['category'] = $row['category'] ?? null;

                $buffer[] = $row;

                if (count($buffer) >= $chunkSize) {
                    $this->upsertProducts($buffer);
                    $buffer = [];
                }
            } catch (\Throwable $e) {
                Log::warning('Invalid product row skipped', [
                    'row' => $row,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (count($buffer)) {
            $this->upsertProducts($buffer);
        }
    }

    protected function upsertProducts(array $rows)
    {
        try {
            Product::upsert(
                $rows,
                ['name', 'category'],
                ['description', 'price', 'image', 'stock']
            );
        } catch (\Throwable $e) {
            Log::error('Failed to upsert product chunk', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
