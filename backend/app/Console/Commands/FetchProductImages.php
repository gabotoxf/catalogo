<?php

namespace App\Console\Commands;

use App\Models\Producto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

// Busca en Wikimedia Commons (sin clave) una foto por nombre de producto
// y guarda la URL en imagen_producto. Omite los que ya tienen imagen,
// así cada deploy solo procesa los nuevos.
class FetchProductImages extends Command
{
    protected $signature = 'catalogo:imagenes {--fresh : Vuelve a buscar imagen aunque el producto ya tenga}';

    protected $description = 'Asigna imagen a cada producto buscándola por su nombre';

    // Términos que en Commons dan mejor resultado que el nombre del producto
    private array $alias = [
        'Yuca x kg' => 'cassava',
        'Malanga x kg' => 'taro',
        'Ñame x kg' => 'yam',
        'Arequipe x 500g' => 'dulce de leche',
        'Maíz pira x kg' => 'maiz',
        'Fríjol cargamanto x kg' => 'frijol',
        'Tomate chonto x kg' => 'tomate',
        'Sandía baby x und' => 'watermelon',
        'Plátano verde x kg' => 'plantain',
        'Plátano hartón x kg' => 'plantain',
        'Mantequilla x 250g' => 'butter',
        'Maíz pira x kg' => 'popcorn',
    ];

    public function handle(): int
    {
        $productos = Producto::query()
            ->when(! $this->option('fresh'), fn ($q) => $q->where(fn ($w) => $w
                ->whereNull('imagen_producto')
                ->orWhere('imagen_producto', 'not like', 'http%')))
            ->get();

        if ($productos->isEmpty()) {
            $this->info('Nada por hacer: todos los productos ya tienen imagen.');
            return self::SUCCESS;
        }

        $ok = 0;
        foreach ($productos as $producto) {
            $termino = $this->alias[$producto->nombre_producto]
                ?? Str::before($producto->nombre_producto, ' x ');
            $url = $this->buscar($termino)
                ?? $this->buscar(strtok($termino, ' ') ?: $termino);

            if ($url) {
                $producto->update(['imagen_producto' => $url]);
                $this->info("OK {$producto->nombre_producto}");
                $ok++;
            } else {
                $this->warn("Sin imagen: {$producto->nombre_producto}");
            }
            usleep(1000000); // 1s entre requests: la API gratuita limita el ritmo
        }

        $this->info("Listo: {$ok}/{$productos->count()} con imagen.");
        return self::SUCCESS;
    }

    private function buscar(string $termino): ?string
    {
        return $this->buscarOpenverse($termino) ?? $this->buscarCommons($termino);
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
        $primera = strtok($objetivo, ' ') ?: $objetivo;

        foreach ($res->json('results') ?? [] as $item) {
            if (! empty($item['mature'])) {
                continue;
            }
            $titulo = $this->normalizar(($item['title'] ?? '').' '.collect($item['tags'] ?? [])->pluck('name')->implode(' '));
            if (str_contains($titulo, $objetivo) || str_contains($titulo, $primera)) {
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
        $primera = strtok($objetivo, ' ') ?: $objetivo;

        foreach ($pages as $page) {
            $titulo = $this->normalizar($page['title'] ?? '');
            if (str_contains($titulo, $objetivo) || str_contains($titulo, $primera)) {
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
