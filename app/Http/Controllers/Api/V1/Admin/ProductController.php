<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreProductRequest;
use App\Http\Requests\V1\UpdateProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use RuntimeException;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $search = trim((string) $request->string('search', ''));
        $categoryId = $request->integer('category_id');
        $brandId = $request->integer('brand_id');
        $available = $request->input('available');
        $sortBy = (string) $request->string('sort_by', 'published_at');
        $sortDir = strtolower((string) $request->string('sort_dir', 'desc'));

        $sortMap = [
            'title' => 'title',
            'date' => 'published_at',
            'published_at' => 'published_at',
            'available' => 'available',
        ];

        $sortColumn = $sortMap[$sortBy] ?? 'published_at';
        $sortDirection = in_array($sortDir, ['asc', 'desc'], true) ? $sortDir : 'desc';

        $query = Product::query()
            ->with(['category', 'brand']);

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId > 0) {
            $query->where('category_id', $categoryId);
        }

        if ($brandId > 0) {
            $query->where('brand_id', $brandId);
        }

        if ($available !== null && $available !== '') {
            $query->where('available', filter_var($available, FILTER_VALIDATE_BOOLEAN));
        }

        $query->orderBy($sortColumn, $sortDirection)->orderByDesc('id');

        $products = $query->paginate(max(1, min($perPage, 100)));

        return response()->json([
            'data' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $payload['slug'] = $payload['slug'] ?? Str::slug($payload['title']);
        $payload['published_at'] = $payload['published_at'] ?? now();

        $product = Product::query()->create($payload)->load(['category', 'brand']);

        return response()->json([
            'message' => 'Producto creado correctamente.',
            'data' => ProductResource::make($product),
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'brand']);

        return response()->json([
            'data' => ProductResource::make($product),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $payload = $request->validated();

        if (empty($payload['slug']) && array_key_exists('title', $payload)) {
            $payload['slug'] = Str::slug($payload['title']);
        }

        $product->update($payload);
        $product->load(['category', 'brand']);

        return response()->json([
            'message' => 'Producto actualizado correctamente.',
            'data' => ProductResource::make($product),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado correctamente.',
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:xlsx,csv,txt'],
            'mode' => ['nullable', 'in:upsert,create-only'],
        ]);

        /** @var UploadedFile $file */
        $file = $payload['file'];
        $mode = $payload['mode'] ?? 'upsert';
        $reader = $this->makeImportReader($file);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $processed = 0;
        $errors = [];
        $maxErrors = 50;

        $reader->open($file->getRealPath());

        try {
            $headers = [];
            $lineNumber = 0;

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $lineNumber++;

                    if ($row->isEmpty()) {
                        continue;
                    }

                    $values = array_map(static fn ($value): string => trim((string) $value), $row->toArray());

                    if ($headers === []) {
                        $headers = $this->normalizeHeaders($values);
                        continue;
                    }

                    $processed++;
                    $rowData = $this->rowToAssoc($headers, $values);

                    try {
                        $result = $this->importSingleProduct($rowData, $mode);

                        if ($result === 'created') {
                            $created++;
                        }

                        if ($result === 'updated') {
                            $updated++;
                        }
                    } catch (\Throwable $exception) {
                        $skipped++;
                        $errors[] = [
                            'row' => $lineNumber,
                            'message' => $exception->getMessage(),
                        ];

                        if (count($errors) >= $maxErrors) {
                            break 2;
                        }
                    }
                }

                break;
            }
        } finally {
            $reader->close();
        }

        return response()->json([
            'message' => 'Importación procesada.',
            'data' => [
                'processed' => $processed,
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
                'errors' => $errors,
            ],
        ]);
    }

    public function importTemplate()
    {
        $headers = [
            'title',
            'slug',
            'category',
            'category_id',
            'brand',
            'brand_id',
            'short_description',
            'description',
            'origin',
            'condition',
            'image_url',
            'tags',
            'available',
            'is_featured',
            'published_at',
        ];

        $sampleRow = [
            'Licuadora Industrial 2L',
            'licuadora-industrial-2l',
            'Licuadoras',
            '',
            'Vitamix',
            '',
            'Licuadora de alto rendimiento',
            'Equipo ideal para cocina profesional con jarra de 2L y motor reforzado.',
            'USA',
            'Nueva',
            'https://example.com/images/licuadora-2l.jpg',
            'cocina,industrial,alta-rotacion',
            '1',
            '0',
            now()->format('Y-m-d H:i:s'),
        ];

        $csv = implode(',', $headers)."\n".implode(',', array_map(static fn ($value): string => '"'.str_replace('"', '""', (string) $value).'"', $sampleRow))."\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="vmfood-product-import-template.csv"',
        ]);
    }

    private function makeImportReader(UploadedFile $file): CsvReader|XlsxReader
    {
        $extension = strtolower((string) $file->getClientOriginalExtension());

        if ($extension === 'xlsx') {
            return new XlsxReader();
        }

        return new CsvReader();
    }

    /**
     * @param list<string> $headers
     * @param list<string> $values
     *
     * @return array<string, string>
     */
    private function rowToAssoc(array $headers, array $values): array
    {
        $data = [];

        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }

            $data[$header] = $values[$index] ?? '';
        }

        return $data;
    }

    /**
     * @param list<string> $headers
     *
     * @return list<string>
     */
    private function normalizeHeaders(array $headers): array
    {
        return array_map(static function (string $header): string {
            $normalized = Str::of($header)
                ->lower()
                ->replace([' ', '-', '.'], '_')
                ->trim()
                ->toString();

            return $normalized;
        }, $headers);
    }

    /**
     * @param array<string, string> $row
     */
    private function importSingleProduct(array $row, string $mode): string
    {
        $title = trim((string) ($row['title'] ?? ''));

        if ($title === '') {
            throw new RuntimeException('La columna title es obligatoria.');
        }

        $slug = trim((string) ($row['slug'] ?? ''));
        if ($slug === '') {
            $slug = Str::slug($title);
        }

        if ($slug === '') {
            throw new RuntimeException('No se pudo generar un slug válido para el producto.');
        }

        $existing = Product::query()->withTrashed()->where('slug', $slug)->first();
        if ($mode === 'create-only' && $existing !== null) {
            throw new RuntimeException("El producto con slug {$slug} ya existe.");
        }

        $categoryId = $this->resolveCategoryId($row);
        $brandId = $this->resolveBrandId($row);
        $publishedAt = trim((string) ($row['published_at'] ?? ''));

        $attributes = [
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'title' => $title,
            'slug' => $slug,
            'short_description' => $this->nullableString($row, 'short_description'),
            'description' => $this->nullableString($row, 'description'),
            'origin' => $this->nullableString($row, 'origin'),
            'condition' => $this->normalizeCondition($row['condition'] ?? null),
            'image_url' => $this->nullableString($row, 'image_url') ?? $this->nullableString($row, 'image'),
            'tags' => $this->parseTags($row['tags'] ?? ''),
            'available' => $this->parseBoolean($row['available'] ?? null, true),
            'is_featured' => $this->parseBoolean($row['is_featured'] ?? null, false),
            'published_at' => $publishedAt !== '' ? $publishedAt : now(),
        ];

        if ($existing !== null) {
            if ($publishedAt === '') {
                unset($attributes['published_at']);
            }

            $existing->fill($attributes);
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->save();

            return 'updated';
        }

        Product::query()->create($attributes);

        return 'created';
    }

    /**
     * @param array<string, string> $row
     */
    private function resolveCategoryId(array $row): ?int
    {
        $categoryIdRaw = trim((string) ($row['category_id'] ?? ''));
        if ($categoryIdRaw !== '' && ctype_digit($categoryIdRaw)) {
            $category = Category::query()->find((int) $categoryIdRaw);

            if ($category !== null) {
                return $category->id;
            }
        }

        $categoryName = trim((string) ($row['category'] ?? $row['category_name'] ?? ''));
        if ($categoryName === '') {
            return null;
        }

        $slug = Str::slug($categoryName);
        if ($slug === '') {
            return null;
        }

        $category = Category::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $categoryName,
                'sort_order' => 0,
                'is_active' => true,
            ]
        );

        return $category->id;
    }

    /**
     * @param array<string, string> $row
     */
    private function resolveBrandId(array $row): ?int
    {
        $brandIdRaw = trim((string) ($row['brand_id'] ?? ''));
        if ($brandIdRaw !== '' && ctype_digit($brandIdRaw)) {
            $brand = Brand::query()->find((int) $brandIdRaw);

            if ($brand !== null) {
                return $brand->id;
            }
        }

        $brandName = trim((string) ($row['brand'] ?? $row['brand_name'] ?? ''));
        if ($brandName === '') {
            return null;
        }

        $slug = Str::slug($brandName);
        if ($slug === '') {
            return null;
        }

        $country = trim((string) ($row['country'] ?? $row['brand_country'] ?? ''));

        $brand = Brand::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $brandName,
                'country' => $country !== '' ? $country : null,
                'is_active' => true,
            ]
        );

        return $brand->id;
    }

    /**
     * @param array<string, string> $row
     */
    private function nullableString(array $row, string $key): ?string
    {
        $value = trim((string) ($row[$key] ?? ''));

        return $value !== '' ? $value : null;
    }

    /**
     * @return array<int, string>
     */
    private function parseTags(?string $raw): array
    {
        if ($raw === null) {
            return [];
        }

        $parts = array_map(static fn (string $part): string => trim($part), explode(',', $raw));
        $parts = array_filter($parts, static fn (string $part): bool => $part !== '');

        return array_values(array_unique($parts));
    }

    private function parseBoolean(?string $value, bool $default): bool
    {
        if ($value === null || trim($value) === '') {
            return $default;
        }

        $normalized = strtolower(trim($value));
        if (in_array($normalized, ['1', 'true', 'yes', 'si', 'sí'], true)) {
            return true;
        }

        if (in_array($normalized, ['0', 'false', 'no'], true)) {
            return false;
        }

        return $default;
    }

    private function normalizeCondition(?string $condition): string
    {
        $normalized = strtolower(trim((string) $condition));

        if ($normalized === 'usada' || $normalized === 'used') {
            return 'Usada';
        }

        return 'Nueva';
    }
}
