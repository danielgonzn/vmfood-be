<?php

namespace App\Filament\Resources\WebContentResource\Pages;

use App\Filament\Resources\WebContentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWebContent extends CreateRecord
{
    protected static string $resource = WebContentResource::class;

    public function getTitle(): string
    {
        return 'Crear contenido web';
    }

    public function getHeading(): string
    {
        return 'Nuevo contenido web';
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Guardar contenido');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Cancelar');
    }
}
