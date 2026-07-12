<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('distribution_systems')) {
            Schema::create('distribution_systems', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('meta_arrecadacao')->default(100);
                $table->unsignedInteger('percentual_distribuicao')->default(25);
                $table->unsignedInteger('rtp_arrecadacao')->default(60);
                $table->unsignedInteger('rtp_distribuicao')->default(100);
                $table->decimal('total_arrecadado', 12, 2)->default(0);
                $table->decimal('total_distribuido', 12, 2)->default(0);
                $table->enum('modo', ['arrecadacao', 'distribuicao'])->default('arrecadacao');
                $table->boolean('ativo')->default(true);
                $table->timestamp('start_cycle_at')->nullable();
                $table->timestamps();
            });
        }

        if (!DB::table('distribution_systems')->exists()) {
            DB::table('distribution_systems')->insert([
                'meta_arrecadacao' => 100,
                'percentual_distribuicao' => 25,
                'rtp_arrecadacao' => 60,
                'rtp_distribuicao' => 100,
                'total_arrecadado' => 0,
                'total_distribuido' => 0,
                'modo' => 'arrecadacao',
                'ativo' => true,
                'start_cycle_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_systems');
    }
};
