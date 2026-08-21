@extends('admin.layouts.app')

@section('title', 'Blocked IPs')
@section('page-title', 'Blocked IPs')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Blocked IPs</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-user-check mr-2 text-primary"></i>Select User First</h3></div>
            <form method="GET" action="{{ route('admin.blocked-ips.index') }}">
                <div class="card-body">
                    <div class="form-group">
                        <label>User</label>
                        <select name="user_id" class="form-control select2" required>
                            <option value="">Choose user</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $selectedUser?->id === $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary"><i class="fas fa-search mr-1"></i> Show Devices</button>
                </div>
            </form>
        </div>

        @if($selectedUser)
        <div class="card mt-3">
            <div class="card-header"><h3><i class="fas fa-ban mr-2 text-danger"></i>Block Manual IP</h3></div>
            <form method="POST" action="{{ route('admin.blocked-ips.block') }}">
                @csrf
                <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                <div class="card-body">
                    <div class="form-group">
                        <label>IP Address</label>
                        <input type="text" name="ip_address" class="form-control" value="{{ $selectedUser->last_login_ip }}" placeholder="192.168.1.10" required>
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Optional note"></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-danger"><i class="fas fa-power-off mr-1"></i> Block & Logout Now</button>
                </div>
            </form>
        </div>
        @endif
    </div>
    <div class="col-md-8">
        @if($selectedUser)
        <div class="card mb-3">
            <div class="card-header">
                <h3><i class="fas fa-laptop mr-2 text-info"></i>Current / Recent Login Devices: {{ $selectedUser->name }}</h3>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>IP Address</th>
                            <th>Device</th>
                            <th>Last Login</th>
                            <th>Status</th>
                            <th>Realtime Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($selectedUser->devices as $device)
                        @php($activeSession = $activeSessionsByIp->get($device->ip_address))
                        <tr>
                            <td>
                                <strong>{{ $device->ip_address }}</strong>
                                @if($selectedUser->last_login_ip === $device->ip_address)
                                    <span class="badge badge-primary ml-1">Last IP</span>
                                @endif
                                @if($activeSession)
                                    <span class="badge badge-success ml-1">Active: {{ $activeSession->sessions }}</span>
                                @endif
                            </td>
                            <td>{{ ucfirst($device->device_type) }}<br><small class="text-muted">{{ $device->device_name }}</small></td>
                            <td>{{ $device->last_login_at?->format('d M Y h:i A') ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $device->is_trusted ? 'success' : 'secondary' }}">{{ $device->is_trusted ? 'Trusted' : 'Untrusted' }}</span>
                                <span class="badge badge-{{ $device->is_blocked ? 'danger' : 'info' }}">{{ $device->is_blocked ? 'Blocked' : 'Allowed' }}</span>
                            </td>
                            <td>
                                @if($device->is_blocked)
                                    <form method="POST" action="{{ route('admin.devices.toggle', [$device, 'unblock']) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-success"><i class="fas fa-unlock mr-1"></i> Unblock</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.devices.toggle', [$device, 'block']) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-power-off mr-1"></i> Block & Logout Now</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    @foreach($sessionIpsWithoutDevices as $sessionIp => $session)
                        <tr>
                            <td>
                                <strong>{{ $sessionIp }}</strong>
                                <span class="badge badge-success ml-1">Active: {{ $session->sessions }}</span>
                            </td>
                            <td>Current session<br><small class="text-muted">Device details will capture after next login.</small></td>
                            <td>{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->format('d M Y h:i A') }}</td>
                            <td><span class="badge badge-info">Allowed</span></td>
                            <td>
                                <form method="POST" action="{{ route('admin.blocked-ips.block') }}">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                                    <input type="hidden" name="ip_address" value="{{ $sessionIp }}">
                                    <input type="hidden" name="reason" value="Blocked from current session list">
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-power-off mr-1"></i> Block & Logout Now</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if($selectedUser->devices->isEmpty() && $sessionIpsWithoutDevices->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No current or captured device found. You can still block this user's last login IP from the left panel.
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-1"></i> Pehle user select karein. Uske baad us user ke login IP/device yahan dikh jayenge.
            </div>
        @endif

        <div class="card">
            <div class="card-header"><h3><i class="fas fa-list mr-2 text-primary"></i>IP Audit</h3></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr><th>IP</th><th>Status</th><th>By</th><th>Reason</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                    @forelse($ips as $ip)
                        <tr>
                            <td>{{ $ip->ip_address }}</td>
                            <td><span class="badge badge-{{ $ip->is_blocked ? 'danger' : 'success' }}">{{ $ip->is_blocked ? 'Blocked' : 'Allowed' }}</span></td>
                            <td>{{ $ip->blockedBy?->name ?? '-' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($ip->reason, 40) }}</td>
                            <td>
                                @if($ip->is_blocked)
                                <form method="POST" action="{{ route('admin.blocked-ips.unblock', $ip) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-success"><i class="fas fa-unlock mr-1"></i> Unblock</button>
                                </form>
                                @else
                                    <span class="text-muted">No action</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No IP records found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $ips->links() }}</div>
        </div>
    </div>
</div>
@endsection
