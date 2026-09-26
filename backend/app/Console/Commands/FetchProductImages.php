<?php

namespace App\Console\Commands;

use App\Models\Producto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

// Busca en Google Images (sin clave, scraping HTML) y descarga la foto
// a public/assets/img/Productos. Guarda solo el nombre del archivo en
// imagen_producto (igual que hace el dashboard al subir). Si Google
// bloquea (429/captcha), cae a Openverse/Commons también descargando local.
class FetchProductImages extends Command
{
    protected $signature = 'catalogo:imagenes {--fresh : Vuelve a buscar imagen aunque el producto ya tenga} {--limit=0 : Maximo de productos a procesar (0 = todos)} {--sync-seeder : Escribe las rutas en el mapa de TiendaSeeder} {--only= : Solo estos ids separados por coma} {--source=all : google, fallback o all}';

    protected $description = 'Busca imagen de cada producto en Google y la guarda local';

    // Consultas desambiguadas para Google: nombres que también son
    // apellidos, lugares u otras cosas devuelven basura sin esto
    private array $googleQuery = [
        'Romero x manojo' => 'planta de romero',
        'Orégano x manojo' => 'planta de oregano',
        'Laurel x manojo' => 'hojas de laurel especia',
        'Tomillo x manojo' => 'planta de tomillo',
        'Perejil x manojo' => 'manojo de perejil fresco',
        'Albahaca x manojo' => 'planta de albahaca',
        'Cilantro x manojo' => 'manojo de cilantro fresco',
        'Harina de maíz x kg' => 'harina de maiz bolsa',
        'Maíz pira x kg' => 'maiz pira grano',
        'Maíz tierno x docena' => 'mazorca de maiz tierno',
        'Avena en hojuelas x kg' => 'avena en hojuelas tazon',
        'Café molido x 500g' => 'cafe molido bolsa',
        'Chocolate de mesa x 500g' => 'chocolate de mesa pastilla',
        'Panela x kg' => 'panela de cana',
        'Miel de abejas x 500g' => 'miel de abejas frasco',
        'Sal marina x kg' => 'sal marina grano',
        'Azúcar morena x kg' => 'azucar morena',
        'Arroz x kg' => 'arroz blanco grano',
        'Lenteja x kg' => 'lentejas grano',
        'Garbanzo x kg' => 'garbanzos grano',
        'Fríjol cargamanto x kg' => 'frijol rojo grano',
        'Kumis x litro' => 'kumis vaso',
        'Cuajada x 250g' => 'cuajada queso fresco',
        'Yogur natural x litro' => 'yogur natural vaso',
        'Queso campesino x 500g' => 'queso campesino bloque',
        'Huevos rojos AA x 30' => 'huevos rojos bandeja',
        'Pitahaya x und' => 'pitahaya amarilla fruta',
        'Uva isabella x kg' => 'uva isabella negra',
        'Guanábana x kg' => 'guanabana fruta',
        'Lulo x kg' => 'lulo fruta',
        'Maracuyá x kg' => 'maracuya fruta',
        'Mora x canasta 500g' => 'moras canasta',
        'Fresa x canasta 500g' => 'fresas canasta',
        'Piña oro miel x und' => 'pina oro miel',
        'Papaya x kg' => 'papaya fruta',
        'Aguacate hass x und' => 'aguacate hass',
        'Mango tommy x kg' => 'mango tommy',
        'Banano x kg' => 'bananos racimo',
        'Arveja verde x kg' => 'arveja verde desgranada',
        'Habichuela x kg' => 'habichuela verde',
        'Remolacha x kg' => 'remolacha',
        'Pepino cohombro x kg' => 'pepino cohombro',
        'Repollo morado x und' => 'repollo morado',
        'Coliflor x und' => 'coliflor',
        'Brócoli x und' => 'brocoli',
        'Espinaca x manojo' => 'espinaca manojo',
        'Acelga x manojo' => 'acelga manojo',
        'Pimentón x kg' => 'pimenton rojo',
        'Lechuga crespa x und' => 'lechuga crespa',
        'Zanahoria x kg' => 'zanahorias',
        'Cebolla cabezona x kg' => 'cebolla cabezona',
        'Tomate chonto x kg' => 'tomate chonto',
        'Apio x manojo' => 'apio rama',
        'Arequipe x 500g' => 'arequipe frasco',
        'Mantequilla x 250g' => 'mantequilla barra',
        'Leche entera x litro' => 'leche entera botella',
        'Papa pastusa x 5kg' => 'papa pastusa bulto',
        'Papa criolla x kg' => 'papa criolla amarilla',
        'Papa sabanera x kg' => 'papa sabanera',
        'Yuca x kg' => 'yuca raiz',
        'Plátano verde x kg' => 'platano verde',
        'Plátano hartón x kg' => 'platano harton',
        'Arracacha x kg' => 'arracacha',
        'Ñame x kg' => 'name tubérculo',
        'Malanga x kg' => 'malanga tuberculo',
        'Batata x kg' => 'batata dulce',
        'Sandía baby x und' => 'sandia baby',
        'Mandarina x kg' => 'mandarinas',
    ];

    // Consultas para fotos de categorías
    private array $catQuery = [
        'Verduras' => 'verduras frescas mercado',
        'Frutas' => 'frutas frescas mercado',
        'Tubérculos' => 'papas tuberculos cosecha',
        'Lácteos y Huevos' => 'queso huevos leche lacteos',
        'Granos y Despensa' => 'granos arroz frijol despensa',
        'Hierbas y Aromáticas' => 'hierbas aromaticas frescas',
    ];

    // Fallback corto en inglés para categorías (más hits exactos)
    private array $catAlias = [
        'Verduras' => 'vegetables',
        'Frutas' => 'fruits',
        'Tubérculos' => 'potatoes',
        'Lácteos y Huevos' => 'cheese',
        'Granos y Despensa' => 'grains',
        'Hierbas y Aromáticas' => 'herbs',
    ];
    // Términos que en Openverse/Commons dan mejor resultado que el español
    private array $alias = [
        'Yuca x kg' => 'cassava root',
        'Malanga x kg' => 'taro root',
        'Ñame x kg' => 'yam tuber',
        'Arequipe x 500g' => 'dulce de leche',
        'Fríjol cargamanto x kg' => 'red beans',
        'Tomate chonto x kg' => 'tomato',
        'Sandía baby x und' => 'watermelon',
        'Plátano verde x kg' => 'plantain',
        'Plátano hartón x kg' => 'plantain',
        'Mantequilla x 250g' => 'butter',
        'Maíz pira x kg' => 'popcorn kernels',
        'Romero x manojo' => 'Rosmarinus officinalis',
        'Orégano x manojo' => 'Origanum vulgare',
        'Laurel x manojo' => 'Laurus nobilis',
        'Tomillo x manojo' => 'Thymus vulgaris',
        'Perejil x manojo' => 'Petroselinum crispum',
        'Albahaca x manojo' => 'Ocimum basilicum',
        'Cilantro x manojo' => 'Coriandrum sativum',
        'Harina de maíz x kg' => 'corn flour',
        'Apio x manojo' => 'celery',
    ];

    public function handle(): int
    {
        $destDir = public_path('assets/img/Productos');
        if (! is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $productos = Producto::query()
            ->when(! $this->option('fresh'), fn ($q) => $q->where(fn ($w) => $w
                ->whereNull('imagen_producto')
                // Si ya es archivo local existente, no reprocesar
                ->orWhere('imagen_producto', 'not like', 'http%')))
            ->get()
            // Salta los locales que ya existen en disco
            ->filter(fn ($p) => $this->option('fresh')
                || ! $p->imagen_producto
                || str_starts_with($p->imagen_producto, 'http')
                || ! file_exists($destDir.'/'.$p->imagen_producto));

        if ((int) $this->option('limit') > 0) {
            $productos = $productos->take((int) $this->option('limit'));
        }

        if ($this->option('only')) {
            $ids = collect(explode(',', $this->option('only')))->map(fn ($i) => (int) trim($i));
            $productos = Producto::whereIn('id_producto', $ids)->get()
                ->filter(fn ($p) => $this->option('fresh')
                    || ! $p->imagen_producto
                    || str_starts_with($p->imagen_producto, 'http')
                    || ! file_exists($destDir.'/'.$p->imagen_producto));
        }

        if ($productos->isEmpty()) {
            if ($this->option('only')) {
                $this->info('Nada por hacer: todos los productos ya tienen imagen.');
                return self::SUCCESS;
            }
            $this->info('Productos: nada por hacer, revisando categorías...');
        } else {
            $ok = 0;
            $map = [];
        foreach ($productos as $producto) {
            // Google rinde mejor con el nombre en español tal cual
            $base = trim(Str::before($producto->nombre_producto, ' x '));
            $slug = Str::slug($base) ?: 'prod-'.$producto->id_producto;
            $archivo = null;
            $fuente = $this->option('source');

            // Google rinde mejor con consulta desambiguada si existe
            $query = $this->googleQuery[$producto->nombre_producto] ?? $base;
            $cands = in_array($this->option('source'), ['all', 'google'], true)
                ? $this->buscarGoogle($query) : [];
            if (! $cands && $fuente === 'all') {
                $this->warn("Google vacío para {$producto->nombre_producto}, usando fallback");
                $fuente = 'fallback';
            }
            foreach ($cands as $cand) {
                $archivo = $this->descargar($cand, $destDir, "{$slug}-{$producto->id_producto}");
                if ($archivo) {
                    $fuente = 'google';
                    break;
                }
            }

            // Fallback: APIs libres, también descargando a local.
            // Solo match exacto: a medias trae tatuajes y fotos random.
            // --source=openverse|commons fuerza una sola API para curar casos.
            $terminoEn = $this->alias[$producto->nombre_producto] ?? $base;
            if (! $archivo && in_array($fuente, ['all', 'fallback', 'openverse'], true)) {
                $url = $this->buscarOpenverse($terminoEn);
                if ($url && ($dl = $this->descargar($url, $destDir, "{$slug}-{$producto->id_producto}"))) {
                    $archivo = $dl;
                    $fuente = 'openverse';
                }
            }
            if (! $archivo && in_array($fuente, ['all', 'fallback', 'commons'], true)) {
                $url = $this->buscarCommons($terminoEn);
                if ($url && ($dl = $this->descargar($url, $destDir, "{$slug}-{$producto->id_producto}"))) {
                    $archivo = $dl;
                    $fuente = 'commons';
                }
            }

            if ($archivo) {
                // Limpia variantes viejas (otra extensión) solo tras éxito
                foreach (glob($destDir."/{$slug}-{$producto->id_producto}.*") ?: [] as $viejo) {
                    if ($viejo !== $destDir.'/'.$archivo) {
                        @unlink($viejo);
                    }
                }
                $producto->update(['imagen_producto' => $archivo]);
                $map[$producto->nombre_producto] = $archivo;
                $this->info("OK [{$fuente}] {$producto->nombre_producto} -> {$archivo}");
                $ok++;
            } else {
                $this->warn("Sin imagen: {$producto->nombre_producto}");
            }
            sleep(random_int(2, 4)); // Google limita el ritmo: pausa aleatoria
            }

            $this->info("Listo: {$ok}/{$productos->count()} con imagen.");
        }

        if (! $this->option('only')) {
            $this->procesarCategorias();
        }

        if ($this->option('sync-seeder')) {
            $this->syncSeeder();
        }

        return self::SUCCESS;
    }

    // Foto por categoría con la misma tubería (Google → fallback), en su carpeta
    private function procesarCategorias(): void
    {
        $destDir = public_path('assets/img/Categorias');
        if (! is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        foreach (\App\Models\Categoria::all() as $cat) {
            if ($cat->imagen_categoria && file_exists($destDir.'/'.$cat->imagen_categoria) && ! $this->option('fresh')) {
                continue;
            }
            $query = $this->catQuery[$cat->nombre_categoria] ?? $cat->nombre_categoria;
            $slug = \Illuminate\Support\Str::slug($cat->nombre_categoria) ?: 'cat-'.$cat->id_categoria;
            $archivo = null;
            foreach ($this->buscarGoogle($query) as $cand) {
                $archivo = $this->descargar($cand, $destDir, "{$slug}-{$cat->id_categoria}");
                if ($archivo) {
                    break;
                }
            }
            if (! $archivo && in_array($this->option('source'), ['all', 'fallback', 'openverse'], true)) {
                $url = $this->buscarOpenverse($this->catAlias[$cat->nombre_categoria] ?? $query);
                if ($url) {
                    $archivo = $this->descargar($url, $destDir, "{$slug}-{$cat->id_categoria}");
                }
            }
            if (! $archivo && in_array($this->option('source'), ['all', 'fallback', 'commons'], true)) {
                $url = $this->buscarCommons($this->catAlias[$cat->nombre_categoria] ?? $query);
                if ($url) {
                    $archivo = $this->descargar($url, $destDir, "{$slug}-{$cat->id_categoria}");
                }
            }
            if ($archivo) {
                $cat->update(['imagen_categoria' => $archivo]);
                $this->info("OK [cat] {$cat->nombre_categoria} -> {$archivo}");
            } else {
                $this->warn("Sin imagen [cat]: {$cat->nombre_categoria}");
            }
            sleep(random_int(2, 4));
        }
    }

    // Reescribe solo el bloque // <imagenes-auto> de TiendaSeeder
    private function syncSeeder(): void
    {
        $path = database_path('seeders/TiendaSeeder.php');
        $actual = Producto::pluck('imagen_producto', 'nombre_producto')
            ->filter(fn ($img) => $img && ! str_starts_with($img, 'http'))
            ->all();
        ksort($actual);

        $lineas = [];
        foreach ($actual as $nombre => $img) {
            $lineas[] = '            '.var_export($nombre, true).' => '.var_export($img, true).',';
        }
        $bloque = "// <imagenes-auto>\n"
            ."        // Mapa nombre => archivo en public/assets/img/Productos.\n"
            ."        // Lo escribe `php artisan catalogo:imagenes --sync-seeder`; no editar a mano.\n"
            ."        \$imagenes = [\n".implode("\n", $lineas)."\n        ];\n"
            ."        // </imagenes-auto>";

        $contenido = file_get_contents($path);
        $nuevo = preg_replace(
            '~// <imagenes-auto>.*?// </imagenes-auto>~s',
            $bloque,
            $contenido
        );

        // Mapa de categorías en el mismo seeder
        $cats = \App\Models\Categoria::pluck('imagen_categoria', 'nombre_categoria')
            ->filter(fn ($img) => $img && ! str_starts_with($img, 'http'))
            ->all();
        ksort($cats);
        $lineasCat = [];
        foreach ($cats as $nombre => $img) {
            $lineasCat[] = '            '.var_export($nombre, true).' => '.var_export($img, true).',';
        }
        $bloqueCat = "// <imagenes-cat-auto>\n"
            ."        // Mapa categoría => archivo en public/assets/img/Categorias.\n"
            ."        // Lo escribe `php artisan catalogo:imagenes --sync-seeder`; no editar a mano.\n"
            ."        \$imagenesCat = [\n".implode("\n", $lineasCat)."\n        ];\n"
            ."        // </imagenes-cat-auto>";
        $nuevo = preg_replace(
            '~// <imagenes-cat-auto>.*?// </imagenes-cat-auto>~s',
            $bloqueCat,
            $nuevo
        );

        file_put_contents($path, $nuevo);
        $this->info('Seeder actualizado: '.count($actual).' rutas + '.count($cats).' cats.');
    }

    // Google Images sin clave: raspa el HTML de tbm=isch y saca las URLs
    // directas ("ou":"https://..."). Devuelve hasta 8 candidatas.
    private function buscarGoogle(string $termino): array
    {
        try {
            $res = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36',
                'Accept-Language' => 'es-CO,es;q=0.9',
            ])->timeout(10)->get('https://www.google.com/search', [
                'tbm' => 'isch',
                'q' => $termino.' producto fresco',
            ]);
        } catch (\Throwable) {
            return [];
        }

        if (! $res->successful()) {
            return [];
        }

        preg_match_all('/"ou":"(https?:[^"]+)"/', $res->body(), $m);
        $urls = array_unique($m[1] ?? []);
        $urls = array_map(fn ($u) => stripcslashes($u), $urls);

        return collect($urls)
            ->reject(fn ($u) => str_contains($u, 'gstatic.com') || str_contains($u, 'google.com'))
            ->filter(fn ($u) => (bool) preg_match('/\.(jpe?g|png|webp)/i', $u))
            ->take(8)->values()->all();
    }

    // Baja la imagen a $destDir y devuelve el nombre de archivo, o null.
    private function descargar(string $url, string $destDir, string $base): ?string
    {
        try {
            $res = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36',
                'Referer' => 'https://www.google.com/',
            ])->timeout(25)->get($url);
        } catch (\Throwable) {
            return null;
        }

        if (! $res->successful()) {
            return null;
        }
        $ct = strtolower($res->header('Content-Type') ?? '');
        if (! str_starts_with($ct, 'image/')) {
            return null;
        }
        $body = $res->body();
        if (strlen($body) < 5000) {
            return null; // ponytail: evita iconos/thumbs rotos
        }
        $ext = match (true) {
            str_contains($ct, 'png') => 'png',
            str_contains($ct, 'webp') => 'webp',
            default => 'jpg',
        };
        $archivo = "{$base}.{$ext}";
        file_put_contents($destDir.'/'.$archivo, $body);

        return $archivo;
    }

    // Openverse (sin clave): agrega Flickr y otros con licencia libre,
    // suele acertar mejor que Commons para comida.
    private function buscarOpenverse(string $termino): ?string
    {
        try {
            $res = Http::withHeaders(['User-Agent' => 'ChaparroEcommerce/1.0'])
                ->timeout(15)->retry(2, 1000)->get('https://api.openverse.org/v1/images/', [
                    'q' => $termino,
                    'page_size' => 10,
                    'filter_dead' => 'false',
                ]);
        } catch (\Throwable) {
            return null;
        }

        if (! $res->successful()) {
            return null;
        }

        $objetivo = $this->normalizar($termino);

        foreach ($res->json('results') ?? [] as $item) {
            if (! empty($item['mature'])) {
                continue;
            }
            $titulo = $this->normalizar(($item['title'] ?? '').' '.collect($item['tags'] ?? [])->pluck('name')->implode(' '));
            if (str_contains($titulo, $objetivo)) {
                if (! empty($item['url'])) {
                    return $item['url'];
                }
            }
        }

        return null;
    }

    private function buscarCommons(string $termino): ?string
    {
        try {
            $res = Http::withHeaders(['User-Agent' => 'ChaparroEcommerce/1.0'])
                ->timeout(15)->retry(2, 1000)->get('https://commons.wikimedia.org/w/api.php', [
                'action' => 'query',
                'format' => 'json',
                'generator' => 'search',
                'gsrsearch' => $termino.' filetype:bitmap',
                'gsrnamespace' => 6,
                'gsrlimit' => 20,
                'prop' => 'imageinfo',
                'iiprop' => 'url',
                'iiurlwidth' => 640,
            ]);
        } catch (\Throwable) {
            return null;
        }

        if (! $res->successful()) {
            return null;
        }

        $pages = $res->json('query.pages') ?? [];
        usort($pages, fn ($a, $b) => ($a['index'] ?? 99) <=> ($b['index'] ?? 99));

        $objetivo = $this->normalizar($termino);

        foreach ($pages as $page) {
            $titulo = $this->normalizar($page['title'] ?? '');
            if (str_contains($titulo, $objetivo)) {
                $url = $page['imageinfo'][0]['thumburl'] ?? $page['imageinfo'][0]['url'] ?? null;
                if ($url) {
                    return $this->limpiar($url);
                }
            }
        }

        return null;
    }

    private function normalizar(string $texto): string
    {
        return strtolower(Str::ascii($texto));
    }

    private function limpiar(string $url): string
    {
        $p = parse_url($url);
        return ($p['scheme'] ?? 'https').'://'.($p['host'] ?? '').($p['path'] ?? '');
    }
}
