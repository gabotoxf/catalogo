<?php

namespace App\Http\Controllers;


use App\Models\Banner;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Pagina Principal',
            'categorias' => Categoria::all(),
            'productos' => Producto::orderBy('id_producto', 'desc')->take(8)->get(),
            'productosConCategoria' => Producto::with('categoria')->orderBy('id_producto', 'desc')->take(8)->get(),
            'banners' => Banner::all(),
        ];

        return response()->json($data);
    }
}
