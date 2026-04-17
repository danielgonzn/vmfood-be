<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $modelLabel = 'producto';

    protected static ?string $pluralModelLabel = 'productos';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Productos';

    protected static ?string $navigationGroup = 'Catálogo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->exists('categories', 'id')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('brand_id')
                    ->label('Marca')
                    ->relationship('brand', 'name')
                    ->exists('brands', 'id')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('title')
                    ->label('Nombre del producto')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->label('URL amigable')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('short_description')
                    ->label('Resumen corto')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('origin')
                    ->label('Origen')
                    ->maxLength(255),
                Forms\Components\TextInput::make('condition')
                    ->label('Estado del producto')
                    ->required(),
                Forms\Components\FileUpload::make('image_url')
                    ->label('Imagen principal')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public'),
                Forms\Components\TextInput::make('gallery_images')
                    ->label('Galería (URLs)')
                    ->helperText('Si agregas varias imágenes, sepáralas por coma.'),
                Forms\Components\TextInput::make('capacity')
                    ->label('Capacidad')
                    ->maxLength(255),
                Forms\Components\TextInput::make('voltage')
                    ->label('Voltaje')
                    ->maxLength(255),
                Forms\Components\TextInput::make('power')
                    ->label('Potencia')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tags')
                    ->label('Etiquetas')
                    ->helperText('Ejemplo: freidora, industrial, acero inoxidable'),
                Forms\Components\Toggle::make('available')
                    ->label('Disponible')
                    ->required(),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Destacado')
                    ->required(),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Publicado el'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Marca')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Producto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL amigable')
                    ->searchable(),
                Tables\Columns\TextColumn::make('short_description')
                    ->label('Resumen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('origin')
                    ->label('Origen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('condition')
                    ->label('Estado'),
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Imagen'),
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Capacidad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('voltage')
                    ->label('Voltaje')
                    ->searchable(),
                Tables\Columns\TextColumn::make('power')
                    ->label('Potencia')
                    ->searchable(),
                Tables\Columns\IconColumn::make('available')
                    ->label('Disponible')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicado')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Eliminado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->modalHeading('Eliminar productos')
                        ->modalDescription('Esta acción eliminará los productos seleccionados de forma permanente.')
                        ->modalSubmitActionLabel('Sí, eliminar')
                        ->modalCancelActionLabel('Cancelar'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
