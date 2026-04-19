<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\Response;

class UserManagement
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $agent = new Agent();

        $deviceName = $agent->browser(); // Chrome, Safari
        $deviceModel = $agent->platform(); // Windows, iOS, Android

        Device::updateOrCreate(
            [
                'user_agent' => $request->userAgent(),

            ],
            [
                'ip_address' => $request->ip(),
                'customer_id'       => auth()->id(),
                'name'   => $deviceName,
                'model'  => $deviceModel,
                'last_seen'  => now(),
            ]
        );
        return $next($request);
    }
}
