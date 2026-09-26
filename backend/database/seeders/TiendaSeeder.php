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
        // <imagenes-cat-auto>
        // Mapa categoría => archivo en public/assets/img/Categorias.
        // Lo escribe `php artisan catalogo:imagenes --sync-seeder`; no editar a mano.
        $imagenesCat = [
            'Frutas' => 'frutas-2.jpg',
            'Granos y Despensa' => 'granos-y-despensa-5.jpg',
            'Hierbas y Aromáticas' => 'hierbas-y-aromaticas-6.jpg',
            'Lácteos y Huevos' => 'lacteos-y-huevos-4.jpg',
            'Tubérculos' => 'tuberculos-3.jpg',
            'Verduras' => 'verduras-1.jpg',
        ];
        // </imagenes-cat-auto>

        $catIds = [];
        foreach (['Verduras', 'Frutas', 'Tubérculos', 'Lácteos y Huevos', 'Granos y Despensa', 'Hierbas y Aromáticas'] as $nombre) {
            $c = Categoria::firstOrCreate(['nombre_categoria' => $nombre]);
            if (isset($imagenesCat[$nombre])
                && (! $c->imagen_categoria || str_starts_with($c->imagen_categoria, 'http'))) {
                $c->update(['imagen_categoria' => $imagenesCat[$nombre]]);
            }
            $catIds[$nombre] = $c->id_categoria;
        }

        // <imagenes-auto>
        // Mapa nombre => archivo en public/assets/img/Productos.
        // Lo escribe `php artisan catalogo:imagenes --sync-seeder`; no editar a mano.
        $imagenes = [
            'Acelga x manojo' => 'acelga-7.jpg',
            'Aguacate hass x und' => 'aguacate-hass-20.jpg',
            'Albahaca x manojo' => 'albahaca-63.jpg',
            'Apio x manojo' => 'apio-16.jpg',
            'Arequipe x 500g' => 'arequipe-47.jpg',
            'Arracacha x kg' => 'arracacha-35.jpg',
            'Arroz x kg' => 'arroz-53.jpg',
            'Arveja verde x kg' => 'arveja-verde-14.jpg',
            'Avena en hojuelas x kg' => 'avena-en-hojuelas-56.jpg',
            'Azúcar morena x kg' => 'azucar-morena-58.jpg',
            'Banano x kg' => 'banano-18.jpg',
            'Batata x kg' => 'batata-39.jpg',
            'Brócoli x und' => 'brocoli-9.jpg',
            'Café molido x 500g' => 'cafe-molido-50.jpg',
            'Cebolla cabezona x kg' => 'cebolla-cabezona-2.jpg',
            'Chocolate de mesa x 500g' => 'chocolate-de-mesa-59.jpg',
            'Cilantro x manojo' => 'cilantro-5.jpg',
            'Coliflor x und' => 'coliflor-10.jpg',
            'Cuajada x 250g' => 'cuajada-44.jpg',
            'Espinaca x manojo' => 'espinaca-8.jpg',
            'Fresa x canasta 500g' => 'fresa-19.jpg',
            'Fríjol cargamanto x kg' => 'frijol-cargamanto-51.jpg',
            'Garbanzo x kg' => 'garbanzo-55.jpg',
            'Guanábana x kg' => 'guanabana-25.jpg',
            'Habichuela x kg' => 'habichuela-15.jpg',
            'Harina de maíz x kg' => 'harina-de-maiz-57.jpg',
            'Huevos rojos AA x 30' => 'huevos-rojos-aa-41.jpg',
            'Kumis x litro' => 'kumis-48.jpg',
            'Laurel x manojo' => 'laurel-68.jpg',
            'Leche entera x litro' => 'leche-entera-43.jpg',
            'Lechuga crespa x und' => 'lechuga-crespa-4.jpg',
            'Lenteja x kg' => 'lenteja-54.jpg',
            'Lulo x kg' => 'lulo-21.jpg',
            'Malanga x kg' => 'malanga-37.jpg',
            'Mandarina x kg' => 'mandarina-29.jpg',
            'Mango tommy x kg' => 'mango-tommy-17.jpg',
            'Mantequilla x 250g' => 'mantequilla-46.jpg',
            'Maracuyá x kg' => 'maracuya-24.jpg',
            'Maíz pira x kg' => 'maiz-pira-62.jpg',
            'Maíz tierno x docena' => 'maiz-tierno-52.jpg',
            'Miel de abejas x 500g' => 'miel-de-abejas-60.jpg',
            'Mora x canasta 500g' => 'mora-26.jpg',
            'Orégano x manojo' => 'oregano-65.jpg',
            'Panela x kg' => 'panela-49.jpg',
            'Papa criolla x kg' => 'papa-criolla-32.jpg',
            'Papa pastusa x 5kg' => 'papa-pastusa-31.jpg',
            'Papa sabanera x kg' => 'papa-sabanera-40.jpg',
            'Papaya x kg' => 'papaya-22.jpg',
            'Pepino cohombro x kg' => 'pepino-cohombro-12.jpg',
            'Perejil x manojo' => 'perejil-66.jpg',
            'Pimentón x kg' => 'pimenton-6.jpg',
            'Pitahaya x und' => 'pitahaya-30.jpg',
            'Piña oro miel x und' => 'pina-oro-miel-23.jpg',
            'Plátano hartón x kg' => 'platano-harton-38.jpg',
            'Plátano verde x kg' => 'platano-verde-34.jpg',
            'Queso campesino x 500g' => 'queso-campesino-42.png',
            'Remolacha x kg' => 'remolacha-13.jpg',
            'Repollo morado x und' => 'repollo-morado-11.jpg',
            'Romero x manojo' => 'romero-64.jpg',
            'Sal marina x kg' => 'sal-marina-61.jpg',
            'Sandía baby x und' => 'sandia-baby-28.jpg',
            'Tomate chonto x kg' => 'tomate-chonto-1.jpg',
            'Tomillo x manojo' => 'tomillo-67.jpg',
            'Uva isabella x kg' => 'uva-isabella-27.png',
            'Yogur natural x litro' => 'yogur-natural-45.jpg',
            'Yuca x kg' => 'yuca-33.jpg',
            'Zanahoria x kg' => 'zanahoria-3.jpg',
            'Ñame x kg' => 'name-36.jpg',
        ];
        // </imagenes-auto>

        // [nombre, descripción, precio COP, stock, categoría]
        $productos = [
            ['Tomate chonto x kg', 'Tomate fresco de cosecha, ideal para guisos y ensaladas.', 4500, 60, 'Verduras'],
            ['Cebolla cabezona x kg', 'Cebolla blanca crocante, base de la cocina colombiana.', 3800, 80, 'Verduras'],
            ['Zanahoria x kg', 'Zanahoria dulce recién arrancada de la tierra.', 3200, 100, 'Verduras'],
            ['Lechuga crespa x und', 'Lechuga verde crocante para ensaladas frescas.', 2500, 50, 'Verduras'],
            ['Cilantro x manojo', 'Manojo de cilantro aromático, recién cortado.', 1500, 120, 'Verduras'],
            ['Pimentón x kg', 'Pimentón rojo y verde, carnoso y de color vivo.', 6500, 40, 'Verduras'],
            ['Acelga x manojo', 'Acelga de penca ancha para sudados y tortillas.', 2000, 60, 'Verduras'],
            ['Espinaca x manojo', 'Espinaca tierna, llena de hierro.', 2500, 55, 'Verduras'],
            ['Brócoli x und', 'Brócoli compacto de color verde intenso.', 3500, 40, 'Verduras'],
            ['Coliflor x und', 'Coliflor blanca de pella firme.', 4000, 35, 'Verduras'],
            ['Repollo morado x und', 'Repollo morado crocante para ensaladas.', 3800, 45, 'Verduras'],
            ['Pepino cohombro x kg', 'Pepino fresco y jugoso, directo del surco.', 2800, 70, 'Verduras'],
            ['Remolacha x kg', 'Remolacha dulce de tierra negra.', 3200, 50, 'Verduras'],
            ['Arveja verde x kg', 'Arveja desgranada, dulce y tierna.', 7500, 30, 'Verduras'],
            ['Habichuela x kg', 'Habichuela larga para sudados.', 5500, 40, 'Verduras'],
            ['Apio x manojo', 'Apio de rama gruesa y aromática.', 2200, 50, 'Verduras'],
            ['Mango tommy x kg', 'Mango dulce de pulpa firme, de temporada.', 5000, 70, 'Frutas'],
            ['Banano x kg', 'Banano de la finca, energía natural.', 2800, 90, 'Frutas'],
            ['Fresa x canasta 500g', 'Fresa roja y jugosa, cultivada en clima frío.', 7000, 35, 'Frutas'],
            ['Aguacate hass x und', 'Aguacate cremoso, punto exacto de maduración.', 4500, 45, 'Frutas'],
            ['Lulo x kg', 'Lulo ácido y aromático, para jugos.', 6000, 30, 'Frutas'],
            ['Papaya x kg', 'Papaya dulce de pulpa anaranjada.', 3500, 60, 'Frutas'],
            ['Piña oro miel x und', 'Piña dulce de corona verde, de Lebrija.', 6000, 40, 'Frutas'],
            ['Maracuyá x kg', 'Maracuyá ácida para jugos y postres.', 5500, 45, 'Frutas'],
            ['Guanábana x kg', 'Guanábana de pulpa blanca para champús.', 7000, 25, 'Frutas'],
            ['Mora x canasta 500g', 'Mora silvestre para jugos y mermeladas.', 6500, 30, 'Frutas'],
            ['Uva isabella x kg', 'Uva negra dulce de clima frío.', 6000, 35, 'Frutas'],
            ['Sandía baby x und', 'Sandía pequeña, roja y refrescante.', 9000, 20, 'Frutas'],
            ['Mandarina x kg', 'Mandarina fácil de pelar, dulce y jugosa.', 4000, 55, 'Frutas'],
            ['Pitahaya x und', 'Pitahaya amarilla de pulpa blanca.', 8000, 20, 'Frutas'],
            ['Papa pastusa x 5kg', 'Bulto de papa pastusa para el mercado de la semana.', 12000, 60, 'Tubérculos'],
            ['Papa criolla x kg', 'Papa criolla amarilla, se deshace en la boca.', 5500, 50, 'Tubérculos'],
            ['Yuca x kg', 'Yuca blandita, de raíz gruesa y buen almidón.', 3000, 80, 'Tubérculos'],
            ['Plátano verde x kg', 'Plátano verde firme para patacones y sancochos.', 3500, 70, 'Tubérculos'],
            ['Arracacha x kg', 'Arracacha amarilla para sopas y purés.', 4500, 40, 'Tubérculos'],
            ['Ñame x kg', 'Ñame de raíz grande, rinde en el sancocho.', 3800, 35, 'Tubérculos'],
            ['Malanga x kg', 'Malanga suave para cremas y sopas.', 4000, 30, 'Tubérculos'],
            ['Plátano hartón x kg', 'Plátano hartón para tajadas maduras.', 3800, 60, 'Tubérculos'],
            ['Batata x kg', 'Batata dulce anaranjada, asada es un manjar.', 3500, 30, 'Tubérculos'],
            ['Papa sabanera x kg', 'Papa sabanera harinosa para caldos.', 4200, 55, 'Tubérculos'],
            ['Huevos rojos AA x 30', 'Bandeja de huevos de gallina libre.', 18000, 40, 'Lácteos y Huevos'],
            ['Queso campesino x 500g', 'Queso fresco de cuajo, hecho en finca.', 14000, 25, 'Lácteos y Huevos'],
            ['Leche entera x litro', 'Leche fresca de ordeño diario, sin adulterar.', 4200, 60, 'Lácteos y Huevos'],
            ['Cuajada x 250g', 'Cuajada blandita para desayunos campesinos.', 6000, 30, 'Lácteos y Huevos'],
            ['Yogur natural x litro', 'Yogur cremoso sin azúcar, de leche entera.', 9000, 25, 'Lácteos y Huevos'],
            ['Mantequilla x 250g', 'Mantequilla batida en finca, con sal.', 12000, 20, 'Lácteos y Huevos'],
            ['Arequipe x 500g', 'Arequipe cocido a leña, de pura leche.', 11000, 25, 'Lácteos y Huevos'],
            ['Kumis x litro', 'Kumis casero, ácido y refrescante.', 7000, 20, 'Lácteos y Huevos'],
            ['Panela x kg', 'Panela de caña de trapiche artesanal.', 6500, 100, 'Granos y Despensa'],
            ['Café molido x 500g', 'Café de origen, tostión media de la región.', 22000, 30, 'Granos y Despensa'],
            ['Fríjol cargamanto x kg', 'Fríjol rojo de grano grande, rinde en la olla.', 9500, 45, 'Granos y Despensa'],
            ['Maíz tierno x docena', 'Docena de mazorcas dulces para envueltos y sopas.', 8000, 35, 'Granos y Despensa'],
            ['Arroz x kg', 'Arroz blanco de grano largo.', 4800, 90, 'Granos y Despensa'],
            ['Lenteja x kg', 'Lenteja pequeña que ablanda rápido.', 7500, 50, 'Granos y Despensa'],
            ['Garbanzo x kg', 'Garbanzo grueso para cocidos.', 8500, 35, 'Granos y Despensa'],
            ['Avena en hojuelas x kg', 'Avena para coladas y desayunos.', 6000, 55, 'Granos y Despensa'],
            ['Harina de maíz x kg', 'Harina precocida para arepas.', 4200, 70, 'Granos y Despensa'],
            ['Azúcar morena x kg', 'Azúcar morena de caña.', 5200, 65, 'Granos y Despensa'],
            ['Chocolate de mesa x 500g', 'Chocolate campesino para el desayuno.', 13000, 30, 'Granos y Despensa'],
            ['Miel de abejas x 500g', 'Miel pura de apiario local.', 18000, 25, 'Granos y Despensa'],
            ['Sal marina x kg', 'Sal de grano para la cocina.', 3000, 80, 'Granos y Despensa'],
            ['Maíz pira x kg', 'Maíz para crispetas caseras.', 5500, 40, 'Granos y Despensa'],
            ['Albahaca x manojo', 'Albahaca fresca para pastas y ensaladas.', 2500, 40, 'Hierbas y Aromáticas'],
            ['Romero x manojo', 'Romero para carnes y asados.', 2500, 35, 'Hierbas y Aromáticas'],
            ['Orégano x manojo', 'Orégano fresco de la huerta.', 2000, 40, 'Hierbas y Aromáticas'],
            ['Perejil x manojo', 'Perejil liso para guisos.', 1500, 60, 'Hierbas y Aromáticas'],
            ['Tomillo x manojo', 'Tomillo aromático para caldos.', 2000, 35, 'Hierbas y Aromáticas'],
            ['Laurel x manojo', 'Hojas de laurel para sancocho.', 2200, 30, 'Hierbas y Aromáticas'],
        ];

        foreach ($productos as [$nombre, $desc, $precio, $stock, $cat]) {
            $p = Producto::firstOrCreate(
                ['nombre_producto' => $nombre],
                [
                    'descripcion_producto' => $desc,
                    'precio_producto' => $precio,
                    'cantidad_producto' => $stock,
                    'categoria_id' => $catIds[$cat],
                    'imagen_producto' => $imagenes[$nombre] ?? null,
                ]
            );
            // Rellena imagen vacía o hotlink viejo con la curada local,
            // sin tocar precio/stock/descripción (los maneja el dashboard)
            if (isset($imagenes[$nombre])
                && (! $p->imagen_producto || str_starts_with($p->imagen_producto, 'http'))) {
                $p->update(['imagen_producto' => $imagenes[$nombre]]);
            }
        }
    }
}
