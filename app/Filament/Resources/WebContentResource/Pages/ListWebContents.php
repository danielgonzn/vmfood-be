<?php

namespace App\Filament\Resources\WebContentResource\Pages;

use App\Filament\Resources\WebContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListWebContents extends ListRecords
{
    protected static string $resource = WebContentResource::class;

    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return MaxWidth::Full;
    }

    public function getTitle(): string
    {
        return 'Contenido web';
    }

    public function getHeading(): string
    {
        return 'Gestor de contenido web';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo contenido'),
        ];
    }
}
