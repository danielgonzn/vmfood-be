<?php

namespace App\Filament\Resources\WebContentResource\Pages;

use App\Filament\Resources\WebContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebContent extends EditRecord
{
    protected static string $resource = WebContentResource::class;

    public function getTitle(): string
    {
        return 'Editar contenido web';
    }

    public function getHeading(): string
    {
        return 'Editar contenido web';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Eliminar')
                ->modalHeading('Eliminar contenido web')
                ->modalDescription('Esta accion eliminara este contenido de forma permanente.')
                ->modalSubmitActionLabel('Si, eliminar')
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
}
