<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Update extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Primero, eliminar las columnas antiguas
        Schema::table('oferta', function (Blueprint $table) {
            $table->dropColumn(['funciones', 'detalles_adicionales']);
        });

        Schema::table('postulante', function (Blueprint $table) {
            $table->dropColumn('informacion_extra');
        });

        Schema::table('formacion_profesional', function (Blueprint $table) {
            $table->dropColumn('descripcion_responsabilidades');
        });

        // Luego, añadir las nuevas columnas
        Schema::table('oferta', function (Blueprint $table) {
            $table->mediumText('funciones')->nullable();
            $table->mediumText('detalles_adicionales')->nullable();
        });

        Schema::table('postulante', function (Blueprint $table) {
            $table->mediumText('informacion_extra')->nullable();
        });

        Schema::table('formacion_profesional', function (Blueprint $table) {
            $table->mediumText('descripcion_responsabilidades')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Para deshacer la migración, revertir los cambios
        Schema::table('oferta', function (Blueprint $table) {
            $table->dropColumn(['funciones', 'detalles_adicionales']);
        });

        Schema::table('postulante', function (Blueprint $table) {
            $table->dropColumn('informacion_extra');
        });

        Schema::table('formacion_profesional', function (Blueprint $table) {
            $table->dropColumn('descripcion_responsabilidades');
        });

        // Aquí se deberían volver a añadir las columnas antiguas si es necesario
        Schema::table('oferta', function (Blueprint $table) {
            $table->text('funciones')->nullable();
            $table->text('detalles_adicionales')->nullable();
        });

        Schema::table('postulante', function (Blueprint $table) {
            $table->string('informacion_extra', 1000)->nullable();
        });

        Schema::table('formacion_profesional', function (Blueprint $table) {
            $table->string('descripcion_responsabilidades', 500)->nullable();
        });
    }

}
