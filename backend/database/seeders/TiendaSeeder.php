<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

// Catálogo inicial de tienda campesina. Idempotente (firstOrCreate):
// se puede correr en cada deploy sin duplicar datos.
class TiendaSeeder extends Seeder
{
    public function run(): void
    {
        $catIds = [];
        foreach (['Verduras', 'Frutas', 'Tubérculos', 'Lácteos y Huevos', 'Granos y Despensa'] as $nombre) {
            $catIds[$nombre] = Categoria::firstOrCreate(['nombre_categoria' => $nombre])->id_categoria;
        }

        // [nombre, descripción, precio COP, stock, categoría]
        $productos = [
            ['Tomate chonto x kg', 'Tomate fresco de cosecha, ideal para guisos y ensaladas.', 4500, 60, 'Verduras'],
            ['Cebolla cabezona x kg', 'Cebolla blanca crocante, base de la cocina colombiana.', 3800, 80, 'Verduras'],
            ['Zanahoria x kg', 'Zanahoria dulce recién arrancada de la tierra.', 3200, 100, 'Verduras'],
            ['Lechuga crespa x und', 'Lechuga verde crocante para ensaladas frescas.', 2500, 50, 'Verduras'],
            ['Cilantro x manojo', 'Manojo de cilantro aromático, recién cortado.', 1500, 120, 'Verduras'],
            ['Pimentón x kg', 'Pimentón rojo y verde, carnoso y de color vivo.', 6500, 40, 'Verduras'],
            ['Mango tommy x kg', 'Mango dulce de pulpa firme, de temporada.', 5000, 70, 'Frutas'],
            ['Banano x kg', 'Banano de la finca, energía natural.', 2800, 90, 'Frutas'],
            ['Fresa x canasta 500g', 'Fresa roja y jugosa, cultivada en clima frío.', 7000, 35, 'Frutas'],
            ['Aguacate hass x und', 'Aguacate cremoso, punto exacto de maduración.', 4500, 45, 'Frutas'],
            ['Lulo x kg', 'Lulo ácido y aromático, para jugos.', 6000, 30, 'Frutas'],
            ['Papa pastusa x 5kg', 'Bulto de papa pastusa para el mercado de la semana.', 12000, 60, 'Tubérculos'],
            ['Papa criolla x kg', 'Papa criolla amarilla, se deshace en la boca.', 5500, 50, 'Tubérculos'],
            ['Yuca x kg', 'Yuca blandita, de raíz gruesa y buen almidón.', 3000, 80, 'Tubérculos'],
            ['Plátano verde x kg', 'Plátano verde firme para patacones y sancochos.', 3500, 70, 'Tubérculos'],
            ['Huevos rojos AA x 30', 'Bandeja de huevos de gallina libre.', 18000, 40, 'Lácteos y Huevos'],
            ['Queso campesino x 500g', 'Queso fresco de cuajo, hecho en finca.', 14000, 25, 'Lácteos y Huevos'],
            ['Leche entera x litro', 'Leche fresca de ordeño diario, sin adulterar.', 4200, 60, 'Lácteos y Huevos'],
            ['Cuajada x 250g', 'Cuajada blandita para desayunos campesinos.', 6000, 30, 'Lácteos y Huevos'],
            ['Panela x kg', 'Panela de caña de trapiche artesanal.', 6500, 100, 'Granos y Despensa'],
            ['Café molido x 500g', 'Café de origen, tostión media de la región.', 22000, 30, 'Granos y Despensa'],
            ['Fríjol cargamanto x kg', 'Fríjol rojo de grano grande, rinde en la olla.', 9500, 45, 'Granos y Despensa'],
            ['Maíz tierno x docena', 'Docena de mazorcas dulces para envueltos y sopas.', 8000, 35, 'Granos y Despensa'],
        ];

        foreach ($productos as [$nombre, $desc, $precio, $stock, $cat]) {
            Producto::firstOrCreate(
                ['nombre_producto' => $nombre],
                [
                    'descripcion_producto' => $desc,
                    'precio_producto' => $precio,
                    'cantidad_producto' => $stock,
                    'categoria_id' => $catIds[$cat],
                ]
            );
        }
    }
}
