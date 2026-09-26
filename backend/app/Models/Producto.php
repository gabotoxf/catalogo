<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;

    protected $fillable = [
        'nombre_producto',
        'slug_producto',
        'descripcion_producto',
        'precio_producto',
        'cantidad_producto',
        'imagen_producto',
        'categoria_id'
    ];

    // Slug automático desde el nombre; se regenera si cambia el nombre
    protected static function booted(): void
    {
        static::saving(function (Producto $p) {
            if (empty($p->slug_producto) || $p->isDirty('nombre_producto')) {
                $base = Str::slug(trim(Str::before($p->nombre_producto, ' x '))) ?: 'producto';
                $slug = $base;
                $i = 2;
                while (static::where('slug_producto', $slug)
                    ->when($p->getKey(), fn ($q) => $q->whereKeyNot($p->getKey()))
                    ->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $p->slug_producto = $slug;
            }
        });
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id_categoria');
    }
}
