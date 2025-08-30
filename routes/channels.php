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


Broadcast::channel('presence.admin-dashboard', function ($user) {
    //
    if (! $user) return false;

    return $user && $user->canAccessDashboard();

    // return [
    //     'id' => $user->id,
    //     'type' => $user instanceof \App\Models\Admin ? 'admin' : 'customer',
    //     'name' => $user->name,
    // ];
});

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
