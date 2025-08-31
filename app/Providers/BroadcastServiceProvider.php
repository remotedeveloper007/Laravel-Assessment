<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes([
            'middleware' => ['web', 'broadcast.any'],
            'prefix' => 'broadcasting/auth'
        ]);
        // Broadcast::routes([
        //     'middleware' => ['web', 'broadcast.any'],
        //     'prefix' => 'broadcasting/admin'
        // ]);

        // Broadcast::routes([
        //     'middleware' => ['web', 'broadcast.any'],
        //     'prefix' => 'broadcasting/customer'
        // ]);        

        require base_path('routes/channels.php');
    }
}
