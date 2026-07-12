<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $email = config('app.admin_email');
        $password = config('app.admin_password');

        if (blank($email) || blank($password)) {
            $this->command?->warn('ADMIN_EMAIL e ADMIN_PASSWORD não configurados; administrador não foi alterado.');
            return;
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $user = User::firstOrNew(['email' => $email]);
        $attributes = ['name' => 'Admin'];

        foreach ([
            'role_id' => 0,
            'status' => 'active',
            'banned' => 0,
            'email_verified_at' => now(),
        ] as $column => $value) {
            if (Schema::hasColumn('users', $column)) {
                $attributes[$column] = $value;
            }
        }

        if (! $user->exists || ! Hash::check($password, (string) $user->password)) {
            $attributes['password'] = $password;
        }

        User::withoutEvents(fn () => $user->forceFill($attributes)->save());

        if (! $user->hasRole('admin')) {
            $user->assignRole($adminRole);
        }

        if (! Schema::hasTable('wallets') || ! Schema::hasColumn('wallets', 'user_id')) {
            return;
        }

        $walletQuery = DB::table('wallets')->where('user_id', $user->id);
        if (Schema::hasColumn('wallets', 'active')) {
            $walletQuery->where('active', 1);
        }

        $hasActiveWallet = $walletQuery
            ->exists();

        if (! $hasActiveWallet) {
            $wallet = ['user_id' => $user->id];
            foreach ([
                'currency' => 'BRL',
                'symbol' => 'R$',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ] as $column => $value) {
                if (Schema::hasColumn('wallets', $column)) {
                    $wallet[$column] = $value;
                }
            }
            DB::table('wallets')->insert($wallet);
        }
    }
}
