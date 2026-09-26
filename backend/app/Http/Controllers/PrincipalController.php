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
            // Acepta slug o id de categoría (el id solo si es numérico: Postgres)
            $catQuery = Categoria::where('slug_categoria', $categoriaId);
            if (is_numeric($categoriaId)) {
                $catQuery->orWhere('id_categoria', (int) $categoriaId);
            }
            $catId = $catQuery->value('id_categoria') ?? $categoriaId;
            $productosQuery->where('categoria_id', $catId);
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

    public function detalleProducto($slug)
    {
        // Acepta slug o id (los links viejos con id siguen funcionando).
        // El id solo se compara si es numérico: en Postgres comparar
        // entero con texto da 500.
        $producto = Producto::with('categoria')
            ->where('slug_producto', $slug)
            ->when(is_numeric($slug), fn ($q) => $q->orWhere('id_producto', (int) $slug))
            ->first();

        if (! $producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $relacionados = Producto::with('categoria:id_categoria,nombre_categoria')
            ->where('categoria_id', $producto->categoria_id)
            ->where('id_producto', '!=', $producto->id_producto)
            ->take(4)->get();

        return response()->json(array_merge(
            $producto->toArray(),
            ['relacionados' => $relacionados]
        ));
    }
}
