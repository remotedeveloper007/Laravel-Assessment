<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Events\UserOnlineStatus;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TouchPresence
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        foreach (['admin','customer'] as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                event(new UserOnlineStatus($user));

                $user->forceFill([
                    'online' => true,
                    'last_seen_at' => now(),
                ])->saveQuietly();
            }
        }         
        return $next($request);
    }
}
