<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id_rol');
            $table->string('nombre_rol', 20);
            $table->string('descripcion_rol', 100)->nullable();
        });

        // Seed roles
        DB::table('roles')->insert([
            ['nombre_rol' => 'admin', 'descripcion_rol' => 'Administrador con acceso total'],
            ['nombre_rol' => 'usuario', 'descripcion_rol' => 'Usuario final de la tienda'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
