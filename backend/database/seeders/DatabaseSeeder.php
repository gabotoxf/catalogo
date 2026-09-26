<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([TiendaSeeder::class]);

        // Slugs de productos sin slug (WithoutModelEvents apaga el evento saving,
        // así que se generan aquí explícito; idempotente en cada deploy)
        $usados = Producto::whereNotNull('slug_producto')->pluck('slug_producto')->all();
        Producto::whereNull('slug_producto')->each(function ($p) use (&$usados) {
            $base = Str::slug(trim(Str::before($p->nombre_producto, ' x '))) ?: 'producto';
            $slug = $base;
            $i = 2;
            while (in_array($slug, $usados)) {
                $slug = $base.'-'.$i++;
            }
            $usados[] = $slug;
            $p->update(['slug_producto' => $slug]);
        });

        // Slugs de categorías (mismo motivo: WithoutModelEvents)
        $usadosCat = Categoria::whereNotNull('slug_categoria')->pluck('slug_categoria')->all();
        Categoria::whereNull('slug_categoria')->each(function ($c) use (&$usadosCat) {
            $base = Str::slug($c->nombre_categoria) ?: 'categoria';
            $slug = $base;
            $i = 2;
            while (in_array($slug, $usadosCat)) {
                $slug = $base.'-'.$i++;
            }
            $usadosCat[] = $slug;
            $c->update(['slug_categoria' => $slug]);
        });

        // Admin inicial (idempotente): sin esto nadie puede entrar al dashboard
        Usuario::firstOrCreate(
            ['usuario_usuario' => 'admin'],
            [
                'nombre_usuario' => 'Admin',
                'apellido_usuario' => 'Chaparro',
                'password_usuario' => Hash::make('admin123'),
                'estado_usuario' => 'activo',
                'rol_usuario' => 1,
            ]
        );
    }
}
