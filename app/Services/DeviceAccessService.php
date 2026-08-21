<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceAccessService
{
    public function inspect(User $user, Request $request): array
    {
        $ip = $request->ip();
        $deviceType = $this->deviceType($request->userAgent());
        $device = UserDevice::where('user_id', $user->id)->where('ip_address', $ip)->first();

        if ($device?->is_blocked) {
            return $this->deny('This device is blocked for your account.');
        }

        if ($deviceType === 'mobile' && !$user->allow_mobile_login) {
            return $this->deny('Mobile login is disabled for your account.');
        }

        if (in_array($deviceType, ['desktop', 'tablet'], true) && !$user->allow_desktop_login) {
            return $this->deny('Laptop/desktop login is disabled for your account.');
        }

        if ($user->trusted_ip_only && !$device?->is_trusted) {
            return $this->deny('Your account is allowed to login only from trusted devices.');
        }

        if ($user->max_active_devices && $this->activeSessionCount($user, $request) >= $user->max_active_devices) {
            return $this->deny("Your account is already active on {$user->max_active_devices} device(s).");
        }

        return ['allowed' => true, 'message' => null, 'device_type' => $deviceType];
    }

    public function remember(User $user, Request $request): UserDevice
    {
        return UserDevice::updateOrCreate(
            ['user_id' => $user->id, 'ip_address' => $request->ip()],
            [
                'device_type' => $this->deviceType($request->userAgent()),
                'device_name' => $this->deviceName($request->userAgent()),
                'user_agent' => $request->userAgent(),
                'last_login_at' => now(),
            ]
        );
    }

    public function logoutSessionsForIp(User $user, string $ipAddress): int
    {
        if (config('session.driver') !== 'database') {
            return 0;
        }

        return DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->where('ip_address', $ipAddress)
            ->delete();
    }

    public function deviceType(?string $userAgent): string
    {
        $agent = strtolower($userAgent ?? '');

        if (str_contains($agent, 'ipad') || str_contains($agent, 'tablet')) {
            return 'tablet';
        }

        if (str_contains($agent, 'mobile') || str_contains($agent, 'android') || str_contains($agent, 'iphone')) {
            return 'mobile';
        }

        return 'desktop';
    }

    protected function deviceName(?string $userAgent): string
    {
        $agent = strtolower($userAgent ?? '');

        $browser = str_contains($agent, 'edg') ? 'Edge'
            : (str_contains($agent, 'chrome') ? 'Chrome'
            : (str_contains($agent, 'firefox') ? 'Firefox'
            : (str_contains($agent, 'safari') ? 'Safari' : 'Browser')));

        $platform = str_contains($agent, 'windows') ? 'Windows'
            : (str_contains($agent, 'mac') ? 'Mac'
            : (str_contains($agent, 'android') ? 'Android'
            : (str_contains($agent, 'iphone') || str_contains($agent, 'ipad') ? 'iOS' : 'Device')));

        return "{$browser} on {$platform}";
    }

    protected function activeSessionCount(User $user, Request $request): int
    {
        if (config('session.driver') !== 'database') {
            return 0;
        }

        return DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->where('last_activity', '>=', now()->subMinutes((int) config('session.lifetime'))->timestamp)
            ->count();
    }

    protected function deny(string $message): array
    {
        return ['allowed' => false, 'message' => $message, 'device_type' => null];
    }
}
