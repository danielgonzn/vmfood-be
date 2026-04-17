<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string
    {
        return 'Crear producto';
    }

    public function getHeading(): string
    {
        return 'Nuevo producto';
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Guardar producto');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['category_id']) && !Category::query()->whereKey($data['category_id'])->exists()) {
            throw ValidationException::withMessages([
                'category_id' => 'La categoría seleccionada ya no existe. Recarga la página y vuelve a intentar.',
            ]);
        }

        if (!empty($data['brand_id']) && !Brand::query()->whereKey($data['brand_id'])->exists()) {
            throw ValidationException::withMessages([
                'brand_id' => 'La marca seleccionada ya no existe. Recarga la página y vuelve a intentar.',
            ]);
        }

        return $data;
    }
}
