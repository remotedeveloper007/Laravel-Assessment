<?php

namespace App\Listeners;

use App\Events\UserOnlineStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Admin;
use App\Models\Customer;

class UpdateUserOnlineStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserOnlineStatus $event): void
    {
        //
        $user = $event->user;

        if ($user instanceof Admin) {

            $user->update(['online' => true, 'last_seen_at' => now()]);

        } elseif ($user instanceof Customer) {

            $user->update(['online' => true, 'last_seen_at' => now()]);
        }        
    }
}
