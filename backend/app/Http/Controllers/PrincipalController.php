<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PrincipalController extends Controller
{
    public function categorias()
    {
        $categorias = Cache::remember('all_categories', 3600, function () {
            return Categoria::all();
        });
        return response()->json($categorias);
    }

    public function productos()
    {
        $productosPorPagina = 10;
        // Optimization: Use select to only get needed columns if possible, but for now just with()
        $productos = Producto::with('categoria:id_categoria,nombre_categoria')
            ->orderBy('id_producto', 'desc')
            ->paginate($productosPorPagina);
        
        $todasCategorias = Cache::remember('all_categories', 3600, function () {
            return Categoria::all();
        });

        return response()->json([
            'title' => 'Productos',
            'productos' => $productos,
            'todasCategorias' => $todasCategorias
        ]);
    }

    public function filtrarProductos(Request $request)
    {
        $minPrecio = $request->input('minPrecio', 0);
        $maxPrecio = $request->input('maxPrecio', 99999999);
        $query = $request->input('query');
        $categoriaId = $request->input('categoria');
        $sort = $request->input('sort');
        $productosPorPagina = 12;

        $productosQuery = Producto::with('categoria')
            ->whereBetween('precio_producto', [$minPrecio, $maxPrecio]);

        if ($query) {
            $productosQuery->where('nombre_producto', 'like', '%' . $query . '%');
        }

        if ($categoriaId) {
            $productosQuery->where('categoria_id', $categoriaId);
        }

        if ($sort === 'price_asc') {
            $productosQuery->orderBy('precio_producto', 'asc');
        } elseif ($sort === 'price_desc') {
            $productosQuery->orderBy('precio_producto', 'desc');
        } elseif ($sort === 'name') {
            $productosQuery->orderBy('nombre_producto', 'asc');
        } else {
            $productosQuery->orderBy('id_producto', 'desc');
        }

        $productos = $productosQuery->paginate($productosPorPagina);

        return response()->json([
            'productos' => $productos->items(),
            'totalPaginas' => $productos->lastPage(),
            'paginaActual' => $productos->currentPage(),
            'total' => $productos->total()
        ]);
    }

    public function filtrarPorCategoria(Request $request)
    {
        return $this->filtrarProductos($request);
    }
}
