<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->repairUsers();
        $this->repairSettings();

        if (! Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('currency', 20)->default('BRL');
                $table->string('symbol', 5)->default('R$');
                $table->decimal('balance', 20, 2)->default(0);
                $table->decimal('balance_withdrawal', 20, 2)->default(0);
                $table->decimal('balance_bonus_rollover', 20, 2)->default(0);
                $table->decimal('balance_deposit_rollover', 20, 2)->default(0);
                $table->decimal('balance_bonus', 20, 2)->default(0);
                $table->decimal('balance_cryptocurrency', 20, 8)->default(0);
                $table->decimal('balance_demo', 20, 8)->default(0);
                $table->decimal('refer_rewards', 20, 2)->default(0);
                $table->boolean('hide_balance')->default(false);
                $table->boolean('active')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('deposits')) {
            Schema::create('deposits', function (Blueprint $table) {
                $table->id();
                $table->string('payment_id')->nullable();
                $table->unsignedBigInteger('user_id')->index();
                $table->decimal('amount', 20, 2)->default(0);
                $table->string('type')->default('pix');
                $table->string('proof')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->string('currency', 50)->nullable();
                $table->string('symbol', 50)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('likes')) {
            Schema::create('likes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('liked_user_id')->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->text('description')->nullable();
                $table->string('page_url')->nullable();
                $table->string('page_action')->nullable();
                $table->timestamps();
            });
        }
    }

    private function repairUsers(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $columns = [
            'cpf' => fn (Blueprint $table) => $table->string('cpf', 20)->nullable()->index(),
            'phone' => fn (Blueprint $table) => $table->string('phone', 30)->nullable()->index(),
            'affiliate_revenue_share' => fn (Blueprint $table) => $table->bigInteger('affiliate_revenue_share')->default(2),
            'affiliate_cpa' => fn (Blueprint $table) => $table->decimal('affiliate_cpa', 20, 2)->default(10),
            'affiliate_baseline' => fn (Blueprint $table) => $table->decimal('affiliate_baseline', 20, 2)->default(40),
            'inviter' => fn (Blueprint $table) => $table->unsignedBigInteger('inviter')->nullable(),
            'inviter_code' => fn (Blueprint $table) => $table->string('inviter_code', 25)->nullable(),
        ];

        foreach ($columns as $name => $addColumn) {
            if (! Schema::hasColumn('users', $name)) {
                Schema::table('users', $addColumn);
            }
        }
    }

    private function repairSettings(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $columns = [
            'revshare_percentage' => fn (Blueprint $table) => $table->bigInteger('revshare_percentage')->default(20),
            'cpa_value' => fn (Blueprint $table) => $table->decimal('cpa_value', 10, 2)->default(10),
            'cpa_baseline' => fn (Blueprint $table) => $table->decimal('cpa_baseline', 10, 2)->default(40),
            'rollover' => fn (Blueprint $table) => $table->bigInteger('rollover')->default(1),
        ];

        foreach ($columns as $name => $addColumn) {
            if (! Schema::hasColumn('settings', $name)) {
                Schema::table('settings', $addColumn);
            }
        }
    }

    public function down(): void
    {
        // Preserve production data.
    }
};
