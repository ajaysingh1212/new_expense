<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LogoutAllUsers extends Command
{
    protected $signature = 'users:logout-all';

    protected $description = 'Logout every user by clearing active sessions and rotating remember tokens.';

    public function handle(): int
    {
        $sessionTable = config('session.table', 'sessions');
        $sessionCount = 0;

        if (config('session.driver') === 'database' && DB::getSchemaBuilder()->hasTable($sessionTable)) {
            $sessionCount = DB::table($sessionTable)->count();
            DB::table($sessionTable)->delete();
        }

        $userCount = 0;
        User::withTrashed()->chunkById(100, function ($users) use (&$userCount) {
            foreach ($users as $user) {
                $user->forceFill(['remember_token' => Str::random(60)])->save();
                $userCount++;
            }
        });

        $this->info("All users logged out. Cleared {$sessionCount} session(s) and rotated remember tokens for {$userCount} user(s).");

        return self::SUCCESS;
    }
}
