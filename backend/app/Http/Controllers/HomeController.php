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
        $productos = Producto::with('categoria:id_categoria,nombre_categoria')->orderBy('id_producto', 'desc')->take(8)->get();
        
        $categorias = Cache::remember('all_categories', 3600, function () {
            return Categoria::all();
        });

        $banners = Cache::remember('all_banners', 3600, function () {
            return Banner::all();
        });

        $data = [
            'title' => 'Pagina Principal',
            'categorias' => $categorias,
            'productosConCategoria' => $productos,
            'banners' => $banners,
        ];

        return response()->json($data);
    }
}
