<?php

namespace App\Providers;

use App\Models\User;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Filament\Support\Assets\Js;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
//        FilamentAsset::register([
//            Js::make('filament-tools', base_path('vendor/sebastiaankloos/filament-code-editor/dist/filament-tools.js')),
//        ]);

        Schema::defaultStringLength(191);

        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $buffer = explode('.', $attribute);
                            $attributeField = array_pop($buffer);
                            $relationPath = implode('.', $buffer);
                            $query->orWhereHas($relationPath, function (Builder $query) use ($attributeField, $searchTerm) {
                                $query->where($attributeField, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });
            return $this;
        });

        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Deposit::observe(\App\Observers\DepositObserver::class);

        $this->ensureDefaultAdmin();
    }

    private function ensureDefaultAdmin(): void
    {
        $email = config('app.admin_email');
        $password = config('app.admin_password');

        if (blank($email) || blank($password)) {
            return;
        }

        try {
            if (
                ! Schema::hasTable('users') ||
                ! Schema::hasTable('roles') ||
                ! Schema::hasTable('model_has_roles') ||
                ! Schema::hasTable('wallets')
            ) {
                return;
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            $adminRole = Role::firstOrCreate([
                'name' => 'admin',
                'guard_name' => 'web',
            ]);

            $user = User::withoutEvents(function () use ($email, $password) {
                $user = User::firstOrNew(['email' => $email]);

                $attributes = [
                    'name' => $user->name ?: 'Admin',
                    'role_id' => 0,
                    'status' => 'active',
                    'banned' => 0,
                    'email_verified_at' => now(),
                ];

                if (! $user->exists || ! Hash::check($password, (string) $user->password)) {
                    $attributes['password'] = $password;
                }

                $user->forceFill($attributes)->save();

                return $user;
            });

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
        } catch (Throwable) {
            return;
        }
    }
}
