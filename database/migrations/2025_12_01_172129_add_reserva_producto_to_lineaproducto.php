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
        Schema::table('lineaproducto', function (Blueprint $table) {
            $table->foreignId('reserva_id')
                ->after('id')
                ->nullable()
                ->references('id')
                ->on('reserva');

            $table->foreignId('producto_id')
                ->after('id')
                ->nullable()
                ->references('id')
                ->on('producto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lineaproducto', function (Blueprint $table) {
            //
        });
    }
};
