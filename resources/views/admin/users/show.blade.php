@extends('admin.layouts.app')

@section('title', $user->name)
@section('page-title', 'User Profile')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center p-0">
                <div style="height:120px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:12px 12px 0 0;position:relative;">
                    <img src="{{ $user->avatarUrl }}" class="user-avatar-xl" style="position:absolute;bottom:-40px;left:50%;transform:translateX(-50%);" alt="">
                </div>
                <div style="padding-top:50px;padding-bottom:20px;">
                    <h4 style="font-family:'Poppins',sans-serif;font-weight:700;color:#1e293b;margin-bottom:4px;">{{ $user->name }}</h4>
                    <div style="color:#64748b;font-size:0.875rem;">@{{ $user->username }}</div>
                    <div class="mt-2">{!! $user->primaryRoleBadge !!}</div>
                    @if($user->designation)
                    <div style="color:#64748b;font-size:0.8rem;margin-top:6px;">{{ $user->designation }}@if($user->department) · {{ $user->department }}@endif</div>
                    @endif
                    <div class="mt-3">
                        @if($user->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row text-center">
                    <div class="col-4">
                        <div style="font-size:1.2rem;font-weight:700;color:#4f46e5;">{{ $user->items->count() }}</div>
                        <div style="font-size:0.7rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Items</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:1.2rem;font-weight:700;color:#4f46e5;">{{ $user->createdUsers->count() }}</div>
                        <div style="font-size:0.7rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Created</div>
                    </div>
                    <div class="col-4">
                        <div style="font-size:1.2rem;font-weight:700;color:#4f46e5;">{{ $user->activityLogs->count() }}</div>
                        <div style="font-size:0.7rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Actions</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="card mt-3">
            <div class="card-header"><h3>Contact Info</h3></div>
            <div class="card-body">
                @if($user->email)
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-envelope text-primary mr-2" style="width:16px;"></i>
                    <span style="font-size:0.875rem;">{{ $user->email }}</span>
                </div>
                @endif
                @if($user->phone)
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-phone text-success mr-2" style="width:16px;"></i>
                    <span style="font-size:0.875rem;">{{ $user->phone }}</span>
                </div>
                @endif
                @if($user->fullAddress)
                <div class="d-flex align-items-start mb-2">
                    <i class="fas fa-map-marker-alt text-danger mr-2 mt-1" style="width:16px;"></i>
                    <span style="font-size:0.875rem;">{{ $user->fullAddress }}</span>
                </div>
                @endif
                @if($user->last_login_at)
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-clock text-info mr-2" style="width:16px;"></i>
                    <span style="font-size:0.75rem;color:#94a3b8;">Last login: {{ $user->last_login_at->diffForHumans() }}</span>
                </div>
                @endif

                {{-- Social Links --}}
                @if($user->facebook || $user->twitter || $user->linkedin || $user->instagram || $user->github || $user->website)
                <hr>
                <div class="d-flex gap-2 flex-wrap">
                    @if($user->facebook)<a href="{{ $user->facebook }}" target="_blank" class="btn btn-sm" style="background:#1877f2;color:#fff;border-radius:50%;width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;"><i class="fab fa-facebook-f"></i></a>@endif
                    @if($user->twitter)<a href="{{ $user->twitter }}" target="_blank" class="btn btn-sm" style="background:#1da1f2;color:#fff;border-radius:50%;width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;"><i class="fab fa-twitter"></i></a>@endif
                    @if($user->linkedin)<a href="{{ $user->linkedin }}" target="_blank" class="btn btn-sm" style="background:#0a66c2;color:#fff;border-radius:50%;width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;"><i class="fab fa-linkedin-in"></i></a>@endif
                    @if($user->instagram)<a href="{{ $user->instagram }}" target="_blank" class="btn btn-sm" style="background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);color:#fff;border-radius:50%;width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;"><i class="fab fa-instagram"></i></a>@endif
                    @if($user->github)<a href="{{ $user->github }}" target="_blank" class="btn btn-sm" style="background:#24292e;color:#fff;border-radius:50%;width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;"><i class="fab fa-github"></i></a>@endif
                    @if($user->website)<a href="{{ $user->website }}" target="_blank" class="btn btn-sm btn-outline-secondary" style="border-radius:20px;font-size:0.75rem;"><i class="fas fa-globe mr-1"></i>Website</a>@endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        @if($user->bio)
        <div class="card mb-3">
            <div class="card-header"><h3>About</h3></div>
            <div class="card-body">
                <p style="color:#475569;font-size:0.875rem;line-height:1.7;margin:0;">{{ $user->bio }}</p>
            </div>
        </div>
        @endif

        {{-- Roles & Permissions --}}
        <div class="card mb-3">
            <div class="card-header"><h3><i class="fas fa-shield-alt mr-2 text-primary"></i>Roles & Permissions</h3></div>
            <div class="card-body">
                <div class="mb-3">
                    <strong style="font-size:0.8rem;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;">Assigned Roles</strong>
                    <div class="mt-2">
                        @forelse($user->roles as $role)
                        <span class="badge mr-1" style="background:{{ $role->color }};color:#fff;padding:6px 12px;font-size:0.8rem;">
                            <i class="{{ $role->icon }} mr-1"></i>{{ $role->name }}
                        </span>
                        @empty
                        <span class="text-muted">No roles assigned</span>
                        @endforelse
                    </div>
                </div>
                <div>
                    <strong style="font-size:0.8rem;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;">Permissions</strong>
                    <div class="mt-2">
                        @foreach($user->getAllPermissions()->groupBy('module') as $module => $perms)
                        <div class="mb-2">
                            <span style="font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;">{{ $module }}</span>
                            <div>
                                @foreach($perms as $perm)
                                <span class="badge badge-light mr-1 mb-1" style="font-size:0.72rem;">{{ $perm->group }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-history mr-2 text-info"></i>Recent Activity</h3></div>
            <div class="card-body p-0">
                @forelse($user->activityLogs->take(8) as $log)
                <div class="d-flex align-items-center px-4 py-2" style="border-bottom:1px solid #f1f5f9;">
                    {!! $log->actionBadge !!}
                    <span class="ml-2" style="font-size:0.875rem;color:#475569;">{{ $log->description }}</span>
                    <span class="ml-auto" style="font-size:0.75rem;color:#94a3b8;">{{ $log->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <div class="text-center py-4 text-muted">No activity yet.</div>
                @endforelse
            </div>
        </div>

        @if(auth()->user()->isSuperAdmin())
        <div class="card mt-3">
            <div class="card-header"><h3><i class="fas fa-laptop-code mr-2 text-danger"></i>Login Devices & IP Control</h3></div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <span class="text-muted d-block small">Max Devices</span>
                        <strong>{{ $user->max_active_devices ?: 'No limit' }}</strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted d-block small">Trusted Only</span>
                        <span class="badge badge-{{ $user->trusted_ip_only ? 'warning' : 'secondary' }}">{{ $user->trusted_ip_only ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted d-block small">Phone Login</span>
                        <span class="badge badge-{{ $user->allow_mobile_login ? 'success' : 'danger' }}">{{ $user->allow_mobile_login ? 'Allowed' : 'Blocked' }}</span>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted d-block small">Laptop Login</span>
                        <span class="badge badge-{{ $user->allow_desktop_login ? 'success' : 'danger' }}">{{ $user->allow_desktop_login ? 'Allowed' : 'Blocked' }}</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.users.block-ip', $user) }}" class="mb-3">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="ip_address" class="form-control" value="{{ $user->last_login_ip }}" placeholder="IP address to block for this user">
                        <input type="text" name="reason" class="form-control" placeholder="Reason">
                        <div class="input-group-append">
                            <button class="btn btn-danger"><i class="fas fa-ban mr-1"></i> Block User IP</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr><th>IP</th><th>Device</th><th>Last Login</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                        @forelse($user->devices as $device)
                            <tr>
                                <td>{{ $device->ip_address }}</td>
                                <td>{{ ucfirst($device->device_type) }}<br><small class="text-muted">{{ $device->device_name }}</small></td>
                                <td>{{ $device->last_login_at?->diffForHumans() ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $device->is_trusted ? 'success' : 'secondary' }}">{{ $device->is_trusted ? 'Trusted' : 'Untrusted' }}</span>
                                    <span class="badge badge-{{ $device->is_blocked ? 'danger' : 'info' }}">{{ $device->is_blocked ? 'Blocked' : 'Allowed' }}</span>
                                </td>
                                <td class="d-flex flex-wrap" style="gap:6px;">
                                    <form method="POST" action="{{ route('admin.devices.toggle', [$device, $device->is_trusted ? 'untrust' : 'trust']) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-{{ $device->is_trusted ? 'secondary' : 'success' }}">
                                            <i class="fas fa-{{ $device->is_trusted ? 'times' : 'check' }} mr-1"></i>{{ $device->is_trusted ? 'Untrust' : 'Trust' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.devices.toggle', [$device, $device->is_blocked ? 'unblock' : 'block']) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-{{ $device->is_blocked ? 'success' : 'danger' }}">
                                            <i class="fas fa-{{ $device->is_blocked ? 'unlock' : 'ban' }} mr-1"></i>{{ $device->is_blocked ? 'Unblock' : 'Block' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No device login has been captured yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="mt-3 d-flex gap-2">
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Back
    </a>
    @can('users.edit')
    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
        <i class="fas fa-edit mr-1"></i> Edit User
    </a>
    @endcan
</div>
@endsection
