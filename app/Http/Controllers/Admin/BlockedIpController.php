<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedIp;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\DeviceAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BlockedIpController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')->orderBy('name')->get();
        $selectedUser = null;
        $activeSessionsByIp = collect();
        $sessionIpsWithoutDevices = collect();

        if ($request->filled('user_id')) {
            $selectedUser = User::with([
                'devices' => fn ($query) => $query->latest('last_login_at'),
            ])->findOrFail($request->integer('user_id'));

            if (config('session.driver') === 'database') {
                $activeSessionsByIp = DB::table(config('session.table', 'sessions'))
                    ->select('ip_address', DB::raw('count(*) as sessions'), DB::raw('max(last_activity) as last_activity'))
                    ->where('user_id', $selectedUser->id)
                    ->where('last_activity', '>=', now()->subMinutes((int) config('session.lifetime'))->timestamp)
                    ->groupBy('ip_address')
                    ->get()
                    ->keyBy('ip_address');

                $deviceIps = $selectedUser->devices->pluck('ip_address');
                $sessionIpsWithoutDevices = $activeSessionsByIp->reject(fn ($session, $ip) => $deviceIps->contains($ip));
            }
        }

        $ips = BlockedIp::with('blockedBy')
            ->latest()
            ->paginate(20);

        return view('admin.blocked-ips.index', compact('ips', 'users', 'selectedUser', 'activeSessionsByIp', 'sessionIpsWithoutDevices'));
    }

    public function block(Request $request, DeviceAccessService $deviceAccess)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'ip_address' => [
                'required',
                'ip',
            ],
            'reason' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $user = User::findOrFail($data['user_id']);

        $ip = BlockedIp::updateOrCreate(
            [
                'ip_address' => $data['ip_address'],
            ],
            [
                'is_blocked' => true,
                'blocked_by' => Auth::id(),
                'blocked_at' => now(),
                'unblocked_at' => null,
                'reason' => $data['reason'] ?? null,
            ]
        );

        UserDevice::updateOrCreate(
            ['user_id' => $user->id, 'ip_address' => $data['ip_address']],
            ['device_type' => 'desktop', 'is_blocked' => true, 'is_trusted' => false]
        );

        $loggedOutSessions = $deviceAccess->logoutSessionsForIp($user, $data['ip_address']);

        ActivityLog::log(
            'blocked_ip',
            "IP address {$data['ip_address']} was blocked for {$user->name}.",
            $user,
            [
                'ip_address' => $data['ip_address'],
                'reason' => $data['reason'] ?? null,
                'logged_out_sessions' => $loggedOutSessions,
            ]
        );

        return redirect()->route('admin.blocked-ips.index', ['user_id' => $user->id])->with(
            'success',
            "IP {$data['ip_address']} blocked for {$user->name}. {$loggedOutSessions} active session(s) logged out immediately."
        );
    }

    public function unblock(BlockedIp $blockedIp)
    {
        $blockedIp->update([
            'is_blocked' => false,
            'unblocked_at' => now(),
        ]);

        ActivityLog::log(
            'unblocked_ip',
            "IP address {$blockedIp->ip_address} was unblocked.",
            $blockedIp,
            [
                'ip_address' => $blockedIp->ip_address,
            ]
        );

        return back()->with(
            'success',
            "IP address {$blockedIp->ip_address} has been unblocked successfully."
        );
    }

    public function blockUserIp(Request $request, User $user, DeviceAccessService $deviceAccess)
    {
        $data = $request->validate([
            'ip_address' => ['required', 'ip'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $device = UserDevice::firstOrCreate(
            ['user_id' => $user->id, 'ip_address' => $data['ip_address']],
            ['device_type' => 'desktop']
        );

        $device->update(['is_blocked' => true, 'is_trusted' => false]);
        $loggedOutSessions = $deviceAccess->logoutSessionsForIp($user, $data['ip_address']);

        ActivityLog::log('blocked_user_ip', "Blocked {$data['ip_address']} for {$user->name}", $user, [
            'ip_address' => $data['ip_address'],
            'reason' => $data['reason'] ?? null,
            'logged_out_sessions' => $loggedOutSessions,
        ]);

        return back()->with('success', "IP {$data['ip_address']} blocked for {$user->name}. {$loggedOutSessions} active session(s) logged out immediately.");
    }

    public function toggleDevice(UserDevice $device, string $action, DeviceAccessService $deviceAccess)
    {
        abort_unless(in_array($action, ['trust', 'untrust', 'block', 'unblock'], true), 404);

        $updates = match ($action) {
            'trust' => ['is_trusted' => true, 'is_blocked' => false],
            'untrust' => ['is_trusted' => false],
            'block' => ['is_blocked' => true, 'is_trusted' => false],
            'unblock' => ['is_blocked' => false],
        };

        $device->update($updates);
        $loggedOutSessions = 0;

        if ($action === 'block') {
            $loggedOutSessions = $deviceAccess->logoutSessionsForIp($device->user, $device->ip_address);
        }

        ActivityLog::log("device_{$action}", ucfirst($action) . "ed {$device->ip_address} for {$device->user->name}", $device->user, [
            'ip_address' => $device->ip_address,
            'device_type' => $device->device_type,
            'logged_out_sessions' => $loggedOutSessions,
        ]);

        $message = "Device {$device->ip_address} updated successfully.";

        if ($action === 'block') {
            $message .= " {$loggedOutSessions} active session(s) logged out immediately.";
        }

        return back()->with('success', $message);
    }
}
