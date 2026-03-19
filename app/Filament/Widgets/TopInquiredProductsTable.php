<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopInquiredProductsTable extends BaseWidget
{
    protected static ?string $heading = 'Top productos mas consultados';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->whereHas('inquiries')
                    ->with(['brand'])
                    ->withCount('inquiries')
                    ->orderByDesc('inquiries_count')
                    ->orderBy('title')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Producto')
                    ->searchable(),
                TextColumn::make('brand.name')
                    ->label('Marca')
                    ->placeholder('Sin marca'),
                TextColumn::make('inquiries_count')
                    ->label('Consultas')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                IconColumn::make('available')
                    ->label('Activo')
                    ->boolean(),
            ]);
    }
}
