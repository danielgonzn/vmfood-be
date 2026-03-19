<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MaintenanceMode extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Modo mantenimiento';

    protected static ?string $title = 'Modo mantenimiento';

    protected static ?string $navigationGroup = 'Configuración';

    protected static string $view = 'filament.pages.maintenance-mode';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::maintenanceConfig());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Estado')
                    ->schema([
                        Forms\Components\Toggle::make('enabled')
                            ->label('Habilitar modo mantenimiento')
                            ->helperText('Si está activo, el frontend mostrará la pantalla de mantenimiento para todos los visitantes.')
                            ->default(false),
                    ]),
                Forms\Components\Section::make('Contenido de la pantalla')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título principal')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('subtitle')
                            ->label('Texto descriptivo')
                            ->required()
                            ->rows(3),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo de contacto')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono de contacto')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->label('Ubicación')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('Número WhatsApp')
                            ->required()
                            ->helperText('Formato recomendado: +58 412-7212203')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('logo_url')
                            ->label('Ruta o URL del logo')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        SiteSetting::setMaintenanceConfig($state);

        Notification::make()
            ->title('Configuración guardada')
            ->body('El modo mantenimiento se actualizó correctamente.')
            ->success()
            ->send();
    }
}
