<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Las URLs de imágenes externas superan 255 caracteres.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE productos ALTER COLUMN imagen_producto TYPE TEXT');
            DB::statement('ALTER TABLE categorias ALTER COLUMN imagen_categoria TYPE TEXT');
        } else {
            DB::statement('ALTER TABLE productos MODIFY imagen_producto TEXT NULL');
            DB::statement('ALTER TABLE categorias MODIFY imagen_categoria TEXT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE productos ALTER COLUMN imagen_producto TYPE VARCHAR(255)');
            DB::statement('ALTER TABLE categorias ALTER COLUMN imagen_categoria TYPE VARCHAR(255)');
        } else {
            DB::statement('ALTER TABLE productos MODIFY imagen_producto VARCHAR(255) NULL');
            DB::statement('ALTER TABLE categorias MODIFY imagen_categoria VARCHAR(255) NULL');
        }
    }
};
