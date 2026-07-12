<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        $attributes = [
            'name' => 'Admin',
            'role_id' => 0,
            'status' => 'active',
            'banned' => 0,
            'email_verified_at' => now(),
        ];

        if (! $user->exists || ! Hash::check($password, (string) $user->password)) {
            $attributes['password'] = $password;
        }

        $user->forceFill($attributes)->save();

        if (! $user->hasRole('admin')) {
            $user->assignRole($adminRole);
        }

        $hasActiveWallet = DB::table('wallets')
            ->where('user_id', $user->id)
            ->where('active', 1)
            ->exists();

        if (! $hasActiveWallet) {
            DB::table('wallets')->insert([
                'user_id' => $user->id,
                'currency' => 'BRL',
                'symbol' => 'R$',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
