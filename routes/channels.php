<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('orders.{customerId}', function ($user, $customerId) {
    if ($user instanceof \App\Models\Customer) {
        return (int)$user->id === (int)$customerId;
    }
    if ($user instanceof \App\Models\Admin) {
        return ['admin' => true, 'name' => $user->name];
    }
    return false;
});

Broadcast::channel('admin-dashboard', function ($user) {
    //
    if (! $user) return false;

    return [
        'id' => $user->id,
        'name' => $user->name,
        'type' => $user instanceof \App\Models\Admin ? 'admin' : 'customer',
    ];
});

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
