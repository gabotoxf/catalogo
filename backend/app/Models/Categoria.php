<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;

    protected $fillable = [
        'nombre_categoria',
        'slug_categoria',
        'imagen_categoria'
    ];

    // Slug automático desde el nombre; se regenera si cambia el nombre
    protected static function booted(): void
    {
        static::saving(function (Categoria $c) {
            if (empty($c->slug_categoria) || $c->isDirty('nombre_categoria')) {
                $base = Str::slug($c->nombre_categoria) ?: 'categoria';
                $slug = $base;
                $i = 2;
                while (static::where('slug_categoria', $slug)
                    ->when($c->getKey(), fn ($q) => $q->whereKeyNot($c->getKey()))
                    ->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $c->slug_categoria = $slug;
            }
        });
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id', 'id_categoria');
    }
}
