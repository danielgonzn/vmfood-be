<?php

namespace App\Filament\Resources\BrandResource\Pages;

use App\Filament\Resources\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrands extends ListRecords
{
    protected static string $resource = BrandResource::class;

    public function getTitle(): string
    {
        return 'Marcas';
    }

    public function getHeading(): string
    {
        return 'Gestión de marcas';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva marca'),
        ];
    }
}
