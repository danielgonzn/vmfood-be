<?php

namespace App\Filament\Widgets;

use App\Models\AdminLoginActivity;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentLoginActivityTable extends BaseWidget
{
    protected static ?string $heading = 'Historial de inicio de sesión';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(AdminLoginActivity::query()->latest('logged_in_at')->limit(10))
            ->columns([
                TextColumn::make('logged_in_at')
                    ->label('Fecha y hora')
                    ->dateTime('d/m/Y H:i'),
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->placeholder('Sin usuario'),
                TextColumn::make('email')
                    ->label('Correo')
                    ->placeholder('No disponible'),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->placeholder('No disponible'),
                TextColumn::make('user_agent')
                    ->label('Dispositivo / Navegador')
                    ->limit(60)
                    ->tooltip(fn (AdminLoginActivity $record): ?string => $record->user_agent),
            ]);
    }
}
