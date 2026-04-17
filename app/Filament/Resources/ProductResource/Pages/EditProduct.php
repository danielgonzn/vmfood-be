<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string
    {
        return 'Editar producto';
    }

    public function getHeading(): string
    {
        return 'Editar producto';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Eliminar')
                ->modalHeading('Eliminar producto')
                ->modalDescription('Esta acción eliminará este producto de forma permanente.')
                ->modalSubmitActionLabel('Sí, eliminar')
                ->modalCancelActionLabel('Cancelar'),
        ];
    }

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Guardar cambios');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar');
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
