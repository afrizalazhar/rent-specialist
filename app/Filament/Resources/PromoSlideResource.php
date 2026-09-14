<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoSlideResource\Pages;
use App\Models\PromoSlide;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromoSlideResource extends Resource
{
    protected static ?string $model = PromoSlide::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function getModelLabel(): string
    {
        return 'Banner Promo';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Banner Promo';
    }

    public static function getNavigationLabel(): string
    {
        return 'Banner Promo';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Pemasaran';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.Identity'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('filament.Title'))
                            ->helperText(__('filament.Promo Title Helper'))
                            ->required()
                            ->maxLength(120),
                        Forms\Components\TextInput::make('alt_text')
                            ->label(__('filament.Alt Text'))
                            ->helperText(__('filament.Alt Text Helper'))
                            ->maxLength(180),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('filament.Image'))
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label(__('filament.Banner Image'))
                            ->helperText(__('filament.Banner Image Helper'))
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('promo')
                            ->maxSize(8192)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('filament.Call To Action'))
                    ->description(__('filament.CTA Helper'))
                    ->schema([
                        Forms\Components\TextInput::make('cta_label')
                            ->label(__('filament.CTA Label'))
                            ->maxLength(40),
                        Forms\Components\TextInput::make('cta_url')
                            ->label(__('filament.CTA URL'))
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('filament.Display'))
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label(__('filament.Sort Order'))
                            ->helperText(__('filament.Sort Order Helper'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('filament.Active'))
                            ->helperText(__('filament.Active Helper'))
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label(__('filament.Preview'))
                    ->disk('public')
                    ->height(56)
                    ->extraImgAttributes(['class' => 'rounded-md object-cover']),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.Title'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cta_label')
                    ->label(__('filament.CTA'))
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('filament.Sort Order'))
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('filament.Active'))
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament.Updated At'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('filament.Active')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('filament.Edit')),
                Tables\Actions\DeleteAction::make()->label(__('filament.Delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('filament.Delete Selected')),
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label(__('filament.New')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromoSlides::route('/'),
            'create' => Pages\CreatePromoSlide::route('/create'),
            'edit' => Pages\EditPromoSlide::route('/{record}/edit'),
        ];
    }
}
