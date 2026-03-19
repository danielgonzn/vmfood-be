<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Http\Controllers\Api\V1\Admin\ProductController as AdminProductController;
use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string
    {
        return 'Productos';
    }

    public function getHeading(): string
    {
        return 'Gestión de productos';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_template')
                ->label('Descargar plantilla')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('admin.products.import-template')),
            Actions\Action::make('import_products')
                ->label('Importar productos')
                ->icon('heroicon-o-arrow-up-tray')
                ->modalHeading('Importar productos de forma masiva')
                ->modalDescription('Carga un archivo Excel (.xlsx) o CSV para crear/actualizar productos en lote.')
                ->modalSubmitActionLabel('Procesar importación')
                ->form([
                    Forms\Components\FileUpload::make('file')
                        ->label('Archivo de importación')
                        ->disk('local')
                        ->directory('imports/products')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/csv',
                            'text/plain',
                        ])
                        ->required(),
                    Forms\Components\Select::make('mode')
                        ->label('Modo de importación')
                        ->options([
                            'upsert' => 'Actualizar si existe / Crear si no existe',
                            'create-only' => 'Solo crear nuevos productos',
                        ])
                        ->default('upsert')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $relativePath = (string) ($data['file'] ?? '');

                    if ($relativePath === '' || !Storage::disk('local')->exists($relativePath)) {
                        Notification::make()
                            ->title('No se pudo encontrar el archivo cargado.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $absolutePath = Storage::disk('local')->path($relativePath);
                    $uploadedFile = new UploadedFile(
                        $absolutePath,
                        basename($absolutePath),
                        null,
                        null,
                        true
                    );

                    try {
                        $request = request()->duplicate(
                            ['mode' => $data['mode'] ?? 'upsert'],
                            null,
                            null,
                            null,
                            ['file' => $uploadedFile]
                        );

                        $response = app(AdminProductController::class)->import($request);
                        $payload = $response->getData(true);
                        $result = $payload['data'] ?? [];

                        $summary = sprintf(
                            'Procesados: %d | Creados: %d | Actualizados: %d | Omitidos: %d',
                            (int) ($result['processed'] ?? 0),
                            (int) ($result['created'] ?? 0),
                            (int) ($result['updated'] ?? 0),
                            (int) ($result['skipped'] ?? 0)
                        );

                        Notification::make()
                            ->title('Importación completada')
                            ->body($summary)
                            ->success()
                            ->send();

                        $errors = $result['errors'] ?? [];
                        if (is_array($errors) && count($errors) > 0) {
                            $firstError = $errors[0]['message'] ?? 'Se encontraron filas con error.';

                            Notification::make()
                                ->title('La importación tuvo algunas filas omitidas')
                                ->body((string) $firstError)
                                ->warning()
                                ->send();
                        }
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title('No se pudo procesar la importación')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    } finally {
                        Storage::disk('local')->delete($relativePath);
                    }
                }),
            Actions\CreateAction::make()
                ->label('Nuevo producto'),
        ];
    }
}
