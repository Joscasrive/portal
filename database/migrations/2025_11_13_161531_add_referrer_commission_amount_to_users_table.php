<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Usa Schema::table para modificar la tabla 'users' existente
        Schema::table('users', function (Blueprint $table) {
            
            $table->decimal('referrer_commission_amount', 10, 2)->nullable()->after('referrer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Define la lógica de reversión: eliminar la columna
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referrer_commission_amount');
        });
    }
};