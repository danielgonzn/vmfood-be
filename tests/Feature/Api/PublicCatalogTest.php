<?php

namespace Tests\Feature\Api;

use Database\Seeders\CatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_products_endpoint_returns_data(): void
    {
        $this->seed(CatalogSeeder::class);

        $response = $this->getJson('/api/v1/catalog/products');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }
}
