<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oferta', function (Blueprint $table) {
            // Añadir las nuevas columnas a la tabla 'oferta'
            $table->decimal('comisiones', 10, 2)->nullable();
            $table->decimal('horasExtras', 10, 2)->nullable();
            $table->decimal('viaticos', 10, 2)->nullable();
            $table->string('comentariosComisiones', 800)->nullable();
            $table->string('comentariosHorasExtras', 800)->nullable();
            $table->string('comentariosViaticos', 800)->nullable();
        });

        Schema::table('educacion_requerida', function (Blueprint $table) {
            // Añadir la columna 'titulo_per' en la tabla 'educacion_requerida'
            $table->string('titulo_per', 800)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oferta', function (Blueprint $table) {
            // Eliminar las columnas si se revierte la migración
            $table->dropColumn(['comisiones', 'horasExtras', 'viaticos', 'comentariosComisiones', 'comentariosHorasExtras', 'comentariosViaticos']);
        });

        Schema::table('educacion_requerida', function (Blueprint $table) {
            // Eliminar la columna 'titulo_per' si se revierte la migración
           $table->dropColumn('titulo_per');
       });
    }
}
