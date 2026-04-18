<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('dashboard_stats', 300, function () {
            return [
                'totalProductos' => Producto::count(),
                'totalCategorias' => Categoria::count(),
                'stockBajo' => Producto::where('cantidad_producto', '<', 10)->count(),
                'valorInventario' => Producto::sum(DB::raw('precio_producto * IFNULL(cantidad_producto, 0)')),
            ];
        });

        return response()->json([
            'stats' => $stats,
            'ultimosProductos' => Producto::with('categoria:id_categoria,nombre_categoria')->orderBy('id_producto', 'desc')->take(5)->get(),
            'categoriasPopulares' => Cache::remember('popular_categories', 3600, function () {
                return Categoria::withCount('productos')->orderBy('productos_count', 'desc')->take(5)->get();
            })
        ]);
    }

    public function getCategorias(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Categoria::withCount('productos');

        if ($search) {
            $query->where('nombre_categoria', 'like', "%{$search}%");
        }

        return response()->json($query->paginate($perPage));
    }

    public function agregarCategoria(Request $request)
    {
        $request->validate([
            'nombre_categoria' => 'required|string|max:255',
            'imagen_categoria' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen_categoria')) {
            $image = $request->file('imagen_categoria');
            $name = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('assets/img/Categorias'), $name);
            $data['imagen_categoria'] = $name;
        }

        $categoria = Categoria::create($data);
        Cache::forget('all_categories');
        Cache::forget('popular_categories');
        Cache::forget('dashboard_stats');
        return response()->json($categoria, 201);
    }

    public function editarCategoria(Request $request, $id)
    {
        $categoria = Categoria::find($id);
        if (!$categoria) return response()->json(['error' => 'No encontrado'], 404);
        
        $request->validate([
            'nombre_categoria' => 'required|string|max:255',
            'imagen_categoria' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen_categoria')) {
            // Delete old image if exists
            if ($categoria->imagen_categoria && file_exists(public_path('assets/img/Categorias/' . $categoria->imagen_categoria))) {
                @unlink(public_path('assets/img/Categorias/' . $categoria->imagen_categoria));
            }

            $image = $request->file('imagen_categoria');
            $name = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('assets/img/Categorias'), $name);
            $data['imagen_categoria'] = $name;
        } else {
            $data['imagen_categoria'] = $request->input('imagen_categoria_actual') ?: $categoria->imagen_categoria;
        }

        $categoria->update($data);
        Cache::forget('all_categories');
        Cache::forget('popular_categories');
        Cache::forget('dashboard_stats');
        return response()->json($categoria);
    }

    public function eliminarCategoria($id)
    {
        $categoria = Categoria::find($id);
        if (!$categoria) return response()->json(['error' => 'No encontrado'], 404);
        
        // Check if there are products in this category
        if (Producto::where('categoria_id', $id)->exists()) {
            return response()->json(['error' => 'No se puede eliminar una categoría que tiene productos'], 400);
        }

        $categoria->delete();
        Cache::forget('all_categories');
        Cache::forget('popular_categories');
        Cache::forget('dashboard_stats');
        return response()->json(['message' => 'Eliminado']);
    }

    public function getProductos(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Producto::with('categoria')->orderBy('id_producto', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre_producto', 'like', "%{$search}%")
                  ->orWhere('descripcion_producto', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate($perPage));
    }

    public function agregarProducto(Request $request)
    {
        $request->validate([
            'nombre_producto' => 'required|string|max:255',
            'precio_producto' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id_categoria',
            'imagen_producto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'descripcion_producto' => 'nullable|string',
            'cantidad_producto' => 'nullable|integer'
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen_producto')) {
            $image = $request->file('imagen_producto');
            $name = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('assets/img/Productos'), $name);
            $data['imagen_producto'] = $name;
        }

        $producto = Producto::create($data);
        Cache::forget('dashboard_stats');
        Cache::forget('popular_categories');
        return response()->json($producto, 201);
    }

    public function editarProducto(Request $request, $id)
    {
        $producto = Producto::find($id);
        if (!$producto) return response()->json(['error' => 'No encontrado'], 404);
        
        $request->validate([
            'nombre_producto' => 'required|string|max:255',
            'precio_producto' => 'required|numeric',
            'categoria_id' => 'required|exists:categorias,id_categoria',
            'imagen_producto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'descripcion_producto' => 'nullable|string',
            'cantidad_producto' => 'nullable|integer'
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen_producto')) {
            // Delete old image
            if ($producto->imagen_producto && file_exists(public_path('assets/img/Productos/' . $producto->imagen_producto))) {
                @unlink(public_path('assets/img/Productos/' . $producto->imagen_producto));
            }

            $image = $request->file('imagen_producto');
            $name = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('assets/img/Productos'), $name);
            $data['imagen_producto'] = $name;
        } else {
            $data['imagen_producto'] = $request->input('imagen_producto_actual') ?: $producto->imagen_producto;
        }

        $producto->update($data);
        Cache::forget('dashboard_stats');
        Cache::forget('popular_categories');
        return response()->json($producto);
    }

    public function eliminarProducto($id)
    {
        $producto = Producto::find($id);
        if (!$producto) return response()->json(['error' => 'No encontrado'], 404);
        
        $producto->delete();
        Cache::forget('dashboard_stats');
        Cache::forget('popular_categories');
        return response()->json(['message' => 'Eliminado']);
    }

    // Métodos para Usuarios
    public function getUsuarios()
    {
        return response()->json(Usuario::all());
    }

    public function agregarUsuario(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|string|max:50',
            'apellido_usuario' => 'required|string|max:50',
            'usuario_usuario' => 'required|string|max:50|unique:usuarios,usuario_usuario',
            'password_usuario' => 'required|string|min:6',
            'rol_usuario' => 'required|integer'
        ]);

        $usuario = Usuario::create([
            'nombre_usuario' => $request->nombre_usuario,
            'apellido_usuario' => $request->apellido_usuario,
            'usuario_usuario' => $request->usuario_usuario,
            'password_usuario' => Hash::make($request->password_usuario),
            'estado_usuario' => $request->estado_usuario ?? 'activo',
            'rol_usuario' => $request->rol_usuario,
        ]);

        return response()->json($usuario, 201);
    }

    public function editarUsuario(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) return response()->json(['error' => 'No encontrado'], 404);
        
        $request->validate([
            'nombre_usuario' => 'required|string|max:50',
            'apellido_usuario' => 'required|string|max:50',
            'usuario_usuario' => 'required|string|max:50|unique:usuarios,usuario_usuario,'.$id.',id_usuario',
            'rol_usuario' => 'required|integer'
        ]);

        $data = [
            'nombre_usuario' => $request->nombre_usuario,
            'apellido_usuario' => $request->apellido_usuario,
            'usuario_usuario' => $request->usuario_usuario,
            'estado_usuario' => $request->estado_usuario,
            'rol_usuario' => $request->rol_usuario,
        ];

        if ($request->filled('password_usuario')) {
            $data['password_usuario'] = Hash::make($request->password_usuario);
        }

        $usuario->update($data);
        return response()->json($usuario);
    }

    public function eliminarUsuario($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) return response()->json(['error' => 'No encontrado'], 404);
        
        $usuario->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
