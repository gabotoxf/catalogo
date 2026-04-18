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
        Schema::table('productos', function (Blueprint $table) {
            $table->index('nombre_producto');
            $table->index('precio_producto');
            $table->index('cantidad_producto');
            $table->index(['precio_producto', 'nombre_producto']);
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->index('nombre_categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex(['nombre_producto']);
            $table->dropIndex(['precio_producto']);
            $table->dropIndex(['cantidad_producto']);
            $table->dropIndex(['precio_producto', 'nombre_producto']);
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->dropIndex(['nombre_categoria']);
        });
    }
};
