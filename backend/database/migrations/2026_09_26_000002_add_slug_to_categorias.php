<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('slug_categoria', 255)->nullable()->unique()->after('nombre_categoria');
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('slug_categoria');
        });
    }
};
