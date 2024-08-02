<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->timestamp('first_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->string('reset_password_token', 250)->nullable();
        });

        // Insertar un usuario inicial
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'adpostulate08@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('postulateadministrador2024.'),
            'created_at' => now(),
            'updated_at' => now(),
            'role_id' => 1, // Asegúrate de que tienes esta columna en tu tabla
            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
