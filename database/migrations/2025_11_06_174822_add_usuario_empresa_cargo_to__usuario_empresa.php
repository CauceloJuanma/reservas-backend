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
        Schema::table('uarioempresa', function (Blueprint $table) {
            $table->foreignId('usuario_id')
                ->after('id')
                ->nullable()
                ->references('id')
                ->on('usuario');
            
            $table->foreignId('empresa_id')
                ->after('usuario_id')
                ->nullable()
                ->references('id')
                ->on('empresa');
            
            $table->foreignId('cargo_id')
                ->after('empresa_id')
                ->nullable()
                ->references('id')
                ->on('cargousuarioempresa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uarioempresa', function (Blueprint $table) {
            //
        });
    }
};
