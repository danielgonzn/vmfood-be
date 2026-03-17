<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Maquinaria Alemana'],
            ['name' => 'Maquinaria Industrial China'],
            ['name' => 'Equipamiento Complementario'],
            ['name' => 'Materias Primas No Cárnicas'],
        ])->mapWithKeys(function (array $item): array {
            $category = Category::query()->updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'name' => $item['name'],
                    'is_active' => true,
                ]
            );

            return [$item['name'] => $category];
        });

        $brands = collect([
            ['name' => 'Handtmann', 'country' => 'Alemania'],
            ['name' => 'Treif', 'country' => 'Alemania'],
            ['name' => 'Poly-Clip', 'country' => 'Alemania'],
            ['name' => 'GQD', 'country' => 'China'],
            ['name' => 'SF', 'country' => 'China'],
            ['name' => 'VM Food', 'country' => 'Venezuela'],
        ])->mapWithKeys(function (array $item): array {
            $brand = Brand::query()->updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'name' => $item['name'],
                    'country' => $item['country'],
                    'is_active' => true,
                ]
            );

            return [$item['name'] => $brand];
        });

        $products = [
            [
                'title' => 'Embutidora al Vacío VF 300',
                'category' => 'Maquinaria Alemana',
                'brand' => 'Handtmann',
                'origin' => 'Alemania',
                'condition' => 'Usada',
                'description' => 'Con elevador para contenedores de 200L y alto desempeño para producción continua.',
                'image_url' => 'https://picsum.photos/seed/vf300/700/500',
                'available' => true,
                'capacity' => '10,000 kg/h',
                'voltage' => '380V',
                'power' => '7 kW',
                'tags' => ['embutidos', 'alta capacidad', 'vacío'],
            ],
            [
                'title' => 'Chuleteadora Modelo 2411',
                'category' => 'Maquinaria Alemana',
                'brand' => 'Treif',
                'origin' => 'Alemania',
                'condition' => 'Usada',
                'description' => 'Cortes precisos para línea cárnica con alta repetibilidad y productividad.',
                'image_url' => 'https://picsum.photos/seed/treif2411/700/500',
                'available' => true,
                'tags' => ['corte', 'cárnico'],
            ],
            [
                'title' => 'Sistema de Clipado FCA 3462',
                'category' => 'Maquinaria Alemana',
                'brand' => 'Poly-Clip',
                'origin' => 'Alemania',
                'condition' => 'Usada',
                'description' => 'Cortadora automática móvil con sistema Polyclip para cierre de embutidos.',
                'image_url' => 'https://picsum.photos/seed/fca3462/700/500',
                'available' => true,
                'tags' => ['clipado', 'embutidos'],
            ],
            [
                'title' => 'Embutidora Neumática GQD30',
                'category' => 'Maquinaria Industrial China',
                'brand' => 'GQD',
                'origin' => 'China',
                'condition' => 'Nueva',
                'description' => 'Tolva de 30L ideal para pequeñas y medianas empresas del rubro alimentario.',
                'image_url' => 'https://picsum.photos/seed/gqd30/700/500',
                'available' => true,
                'capacity' => '30 L',
                'tags' => ['embutidos', 'pyme'],
            ],
            [
                'title' => 'Embutidora de Pistón SF-260',
                'category' => 'Maquinaria Industrial China',
                'brand' => 'SF',
                'origin' => 'China',
                'condition' => 'Nueva',
                'description' => 'Equipo de pistón para producción de embutidos con excelente estabilidad de flujo.',
                'image_url' => 'https://picsum.photos/seed/sf260/700/500',
                'available' => true,
                'capacity' => '400 kg/h',
                'power' => '750 W',
                'tags' => ['embutidos', 'pistón'],
            ],
            [
                'title' => 'Tripas de Colágeno',
                'category' => 'Materias Primas No Cárnicas',
                'brand' => 'VM Food',
                'origin' => 'Importado',
                'condition' => 'Nueva',
                'description' => 'Tripas para salchichas y jamones con excelente desempeño en proceso.',
                'image_url' => 'https://picsum.photos/seed/tripascolageno/700/500',
                'available' => true,
                'tags' => ['insumos', 'embutidos'],
            ],
        ];

        foreach ($products as $item) {
            $category = $categories[$item['category']] ?? null;
            $brand = $brands[$item['brand']] ?? null;

            Product::query()->updateOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'title' => $item['title'],
                    'category_id' => $category?->id,
                    'brand_id' => $brand?->id,
                    'short_description' => $item['description'],
                    'description' => $item['description'],
                    'origin' => $item['origin'],
                    'condition' => $item['condition'],
                    'image_url' => $item['image_url'],
                    'gallery_images' => [$item['image_url']],
                    'capacity' => $item['capacity'] ?? null,
                    'voltage' => $item['voltage'] ?? null,
                    'power' => $item['power'] ?? null,
                    'tags' => $item['tags'] ?? [],
                    'available' => $item['available'],
                    'published_at' => now(),
                ]
            );
        }
    }
}
