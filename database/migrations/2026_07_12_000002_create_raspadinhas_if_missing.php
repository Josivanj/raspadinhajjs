<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('raspadinhas')) {
            Schema::create('raspadinhas', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('image')->nullable();
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2);
                $table->decimal('max_prize', 12, 2)->default(0);
                $table->string('category')->default('raspadinha');
                $table->decimal('backend_cost', 10, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->unsignedInteger('win_chance_percentage')->default(1);
                $table->timestamps();
            });
        }

        if (DB::table('raspadinhas')->count() === 0) {
            $now = now();
            DB::table('raspadinhas')->insert([
                ['name' => 'Raspadinha Premiada', 'description' => 'Raspe e concorra a prêmios instantâneos.', 'price' => 1, 'max_prize' => 1000, 'category' => 'popular', 'backend_cost' => 1, 'is_active' => 1, 'sort_order' => 1, 'win_chance_percentage' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Raspadinha 5 Mil', 'description' => 'Prêmios de até cinco mil reais.', 'price' => 2, 'max_prize' => 5000, 'category' => 'popular', 'backend_cost' => 2, 'is_active' => 1, 'sort_order' => 2, 'win_chance_percentage' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Raspadinha 10 Mil', 'description' => 'Sua chance de ganhar até dez mil reais.', 'price' => 5, 'max_prize' => 10000, 'category' => 'premium', 'backend_cost' => 5, 'is_active' => 1, 'sort_order' => 3, 'win_chance_percentage' => 1, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }
    }

    public function down(): void
    {
        // Preserve production scratch-card data.
    }
};
