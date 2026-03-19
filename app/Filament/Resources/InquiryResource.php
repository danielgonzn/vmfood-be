<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages;
use App\Models\Inquiry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static ?string $modelLabel = 'consulta';

    protected static ?string $pluralModelLabel = 'consultas';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Consultas';

    protected static ?string $navigationGroup = 'Comercial';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos del contacto')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->disabled(),
                        Forms\Components\TextInput::make('company')
                            ->label('Empresa')
                            ->disabled(),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo')
                            ->disabled(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->disabled(),
                    ])->columns(2),
                Forms\Components\Section::make('Detalle')
                    ->schema([
                        Forms\Components\TextInput::make('product.title')
                            ->label('Producto relacionado')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'new' => 'Nueva',
                                'contacted' => 'Contactada',
                                'closed' => 'Cerrada',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('message')
                            ->label('Mensaje')
                            ->rows(8)
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recibida')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Nueva',
                        'contacted' => 'Contactada',
                        'closed' => 'Cerrada',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'contacted' => 'info',
                        'closed' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company')
                    ->label('Empresa')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('product.title')
                    ->label('Producto')
                    ->placeholder('General')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('message')
                    ->label('Mensaje')
                    ->limit(70)
                    ->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'new' => 'Nueva',
                        'contacted' => 'Contactada',
                        'closed' => 'Cerrada',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Gestionar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionadas'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInquiries::route('/'),
            'edit' => Pages\EditInquiry::route('/{record}/edit'),
        ];
    }
}
