<?php

namespace App\Http\Middleware;

use App\Services\DeviceAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBlockedIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        if ($request->user()) {
            $access = app(DeviceAccessService::class)->inspect($request->user(), $request);

            if (!$access['allowed']) {
                return $this->deny($request, $ip, $access['message']);
            }
        }

        return $next($request);
    }

    protected function deny(Request $request, string $ip, ?string $reason): Response
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->view('errors.ip-blocked', [
            'ip' => $ip,
            'reason' => $reason,
        ], 403);
    }
}
