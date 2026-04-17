<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')
            ->whereNotNull('brand_id')
            ->whereNotExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('brands')
                    ->whereColumn('brands.id', 'products.brand_id');
            })
            ->update([
                'brand_id' => null,
                'updated_at' => now(),
            ]);

        DB::table('products')
            ->whereNotNull('category_id')
            ->whereNotExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('categories')
                    ->whereColumn('categories.id', 'products.category_id');
            })
            ->update([
                'category_id' => null,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // This migration only normalizes orphan foreign keys to null.
    }
};
