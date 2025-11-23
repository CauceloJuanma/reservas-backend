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
        Schema::table('empresa', function (Blueprint $table) {
            $table->foreignId('estado_id')
                ->after('usuario_id')
                ->nullable()
                ->references('id')
                ->on('estadoempresa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropForeign([ 'estado_id' ]);
            $table->dropColumn('estado_id');
        });
    }
};
