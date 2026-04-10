<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebContentResource\Pages;
use App\Models\WebContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WebContentResource extends Resource
{
    protected static ?string $model = WebContent::class;

    protected static ?string $modelLabel = 'contenido web';

    protected static ?string $pluralModelLabel = 'contenidos web';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Contenido Web';

    protected static ?string $navigationGroup = 'Sitio Web';

    protected static ?int $navigationSort = 1;

    protected static function sectionOptions(): array
    {
        return [
            'home' => 'Inicio',
            'about' => 'Nosotros',
            'catalog' => 'Catálogo',
            'process' => 'Proceso',
            'stats' => 'Estadísticas',
            'faq' => 'Preguntas frecuentes',
            'contact' => 'Contacto',
            'location' => 'Ubicación',
            'banners' => 'Banners',
            'footer' => 'Pie de página',
        ];
    }

    protected static function keyOptions(): array
    {
        return [
            'home_hero' => 'Inicio: Banner principal',
            'home_hero_secondary' => 'Inicio: Banner secundario',
            'about_story' => 'Nosotros: Historia de la empresa',
            'about_mission' => 'Nosotros: Misión',
            'about_vision' => 'Nosotros: Visión',
            'catalog_promo' => 'Catálogo: Promoción destacada',
            'process_step_1' => 'Proceso: Paso 1',
            'process_step_2' => 'Proceso: Paso 2',
            'process_step_3' => 'Proceso: Paso 3',
            'process_step_4' => 'Proceso: Paso 4',
            'stats_clients' => 'Estadísticas: Clientes',
            'stats_products' => 'Estadísticas: Productos',
            'stats_experience' => 'Estadísticas: Años de experiencia',
            'faq_shipping' => 'Preguntas frecuentes: Envío',
            'faq_returns' => 'Preguntas frecuentes: Devoluciones',
            'contact_main' => 'Contacto: Información principal',
            'location_main' => 'Ubicación: Sede principal',
        ];
    }

    protected static function sectionLabel(string $section): string
    {
        return static::sectionOptions()[$section] ?? $section;
    }

    protected static function keyLabel(string $key): string
    {
        return static::keyOptions()[$key] ?? $key;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('content_tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\Select::make('section')
                                    ->label('Sección')
                                    ->options(static::sectionOptions())
                                    ->required()
                                    ->searchable(),
                                Forms\Components\Select::make('key')
                                    ->label('Elemento de contenido')
                                    ->helperText('Selecciona la opcion que deseas editar.')
                                    ->options(static::keyOptions())
                                    ->required()
                                    ->searchable()
                                    ->unique(ignoreRecord: true),
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre visible en el panel')
                                    ->required()
                                    ->maxLength(160),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Orden')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true),
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Publicado')
                                    ->helperText('Si esta desactivado, el contenido quedara como borrador.')
                                    ->default(true),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Fecha de publicacion')
                                    ->helperText('Opcional: programa cuando debe mostrarse este contenido.'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Textos')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Título')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('subtitle')
                                    ->label('Subtítulo')
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('body')
                                    ->label('Contenido')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Imágenes y Banners')
                            ->schema([
                                Forms\Components\FileUpload::make('image_path')
                                    ->label('Imagen principal')
                                    ->image()
                                    ->disk('public')
                                    ->directory('website/content')
                                    ->visibility('public'),
                                Forms\Components\FileUpload::make('banner_path')
                                    ->label('Banner')
                                    ->image()
                                    ->disk('public')
                                    ->directory('website/banners')
                                    ->visibility('public'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Acciones y Meta')
                            ->schema([
                                Forms\Components\TextInput::make('cta_text')
                                    ->label('Texto de botón CTA')
                                    ->maxLength(120),
                                Forms\Components\TextInput::make('cta_url')
                                    ->label('URL de botón CTA')
                                    ->url()
                                    ->maxLength(255),
                                Forms\Components\KeyValue::make('meta')
                                    ->label('Metadatos')
                                    ->keyLabel('Clave')
                                    ->valueLabel('Valor')
                                    ->reorderable()
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('section')
                    ->label('Sección')
                    ->formatStateUsing(fn (string $state): string => static::sectionLabel($state))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->label('Elemento de contenido')
                    ->formatStateUsing(fn (string $state): string => static::keyLabel($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre visible')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publicado')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicacion')
                    ->since(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('section')
                    ->label('Sección')
                    ->options(static::sectionOptions()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publicado'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->modalHeading('Eliminar contenidos web')
                        ->modalDescription('Esta accion eliminara los elementos seleccionados de forma permanente.')
                        ->modalSubmitActionLabel('Si, eliminar')
                        ->modalCancelActionLabel('Cancelar'),
                ]),
            ])
            ->defaultSort('sort_order');
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
            'index' => Pages\ListWebContents::route('/'),
            'create' => Pages\CreateWebContent::route('/create'),
            'edit' => Pages\EditWebContent::route('/{record}/edit'),
        ];
    }
}
