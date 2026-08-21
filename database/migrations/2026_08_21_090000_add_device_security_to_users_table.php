<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('max_active_devices')->nullable()->after('last_login_ip');
            $table->boolean('trusted_ip_only')->default(false)->after('max_active_devices');
            $table->boolean('allow_mobile_login')->default(true)->after('trusted_ip_only');
            $table->boolean('allow_desktop_login')->default(true)->after('allow_mobile_login');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'max_active_devices',
                'trusted_ip_only',
                'allow_mobile_login',
                'allow_desktop_login',
            ]);
        });
    }
};
