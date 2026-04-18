<?php

namespace App\Http\Controllers;


use App\Models\Banner;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')->orderBy('id_producto', 'desc')->take(8)->get();
        
        $categorias = Cache::remember('all_categories', 3600, function () {
            return Categoria::all();
        });

        $data = [
            'title' => 'Pagina Principal',
            'categorias' => $categorias,
            'productosConCategoria' => $productos,
            'banners' => Banner::all(),
        ];

        return response()->json($data);
    }
}
