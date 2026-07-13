<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $columns = [
            'sharkpay_is_enable' => fn (Blueprint $table) => $table->boolean('sharkpay_is_enable')->default(false),
            'ondapay_is_enable' => fn (Blueprint $table) => $table->boolean('ondapay_is_enable')->default(false),
            'bspay_is_enable' => fn (Blueprint $table) => $table->boolean('bspay_is_enable')->default(false),
            'disable_spin' => fn (Blueprint $table) => $table->boolean('disable_spin')->default(false),
            'disable_rollover' => fn (Blueprint $table) => $table->tinyInteger('disable_rollover')->default(0),
        ];

        foreach ($columns as $name => $addColumn) {
            if (! Schema::hasColumn('settings', $name)) {
                Schema::table('settings', $addColumn);
            }
        }
    }

    public function down(): void
    {
        // Preserve production settings.
    }
};
