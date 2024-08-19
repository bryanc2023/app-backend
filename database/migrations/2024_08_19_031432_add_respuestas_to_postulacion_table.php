<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRespuestasToPostulacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postulacion', function (Blueprint $table) {
            $table->json('respuestas')->nullable()->after('total_evaluacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postulacion', function (Blueprint $table) {
            $table->dropColumn('respuestas');
        });
    }
}