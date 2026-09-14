<?php

namespace App\Filament\Resources;

use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
use App\Filament\Resources\VehicleResource\Pages;
use App\Models\Branch;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function getModelLabel(): string
    {
        return __('filament.Vehicle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.Vehicles');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.Master Data');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.Identity'))
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label(__('filament.Branch'))
                            ->relationship('branch', 'name')
                            ->default(fn (): ?int => Branch::query()->value('id'))
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->label(__('filament.Type'))
                            ->options(self::vehicleTypeOptions())
                            ->default(VehicleType::Car->value)
                            ->live()
                            ->required(),
                        Forms\Components\TextInput::make('make')
                            ->label(__('filament.Make'))
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('model')
                            ->label(__('filament.Model'))
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('year')
                            ->label(__('filament.Year'))
                            ->numeric()
                            ->minValue(1950)
                            ->maxValue(2100)
                            ->required(),
                        Forms\Components\TextInput::make('plate_number')
                            ->label(__('filament.Plate Number'))
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('color')
                            ->label(__('filament.Color'))
                            ->maxLength(50),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('filament.Specs'))
                    ->description(__('filament.Specs Description'))
                    ->schema(self::specFields())
                    ->columns(2),

                Forms\Components\Section::make(__('filament.Pricing'))
                    ->schema([
                        Forms\Components\TextInput::make('daily_rate')
                            ->label(__('filament.Daily Rate'))
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\TextInput::make('weekly_rate')
                            ->label(__('filament.Weekly Rate'))
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('monthly_rate')
                            ->label(__('filament.Monthly Rate'))
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make(__('filament.Media'))
                    ->schema([
                        Forms\Components\FileUpload::make('photos')
                            ->label(__('filament.Photos'))
                            ->image()
                            ->multiple()
                            ->disk('public')
                            ->directory('vehicles')
                            ->maxSize(5120)
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('filament.Status'))
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('filament.Status'))
                            ->options(self::editableStatusOptions())
                            ->default(VehicleStatus::Available->value)
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('filament.Notes'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_name')
                    ->label(__('filament.Vehicle'))
                    ->getStateUsing(fn (Vehicle $record): string => $record->displayName())
                    ->searchable(['make', 'model', 'plate_number'])
                    ->sortable(['make', 'model', 'year']),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('filament.Type'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => VehicleType::from(self::enumValue($state))->labelId())
                    ->color(fn ($state): string => match (VehicleType::from(self::enumValue($state))) {
                        VehicleType::Car => 'gray',
                        VehicleType::Suv => 'warning',
                        VehicleType::Motorcycle => 'info',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.Status'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => VehicleStatus::from(self::enumValue($state))->label())
                    ->color(fn ($state): string => match (VehicleStatus::from(self::enumValue($state))) {
                        VehicleStatus::Available => 'success',
                        VehicleStatus::OnRent => 'warning',
                        VehicleStatus::Maintenance => 'gray',
                        VehicleStatus::OutOfService => 'danger',
                        VehicleStatus::ReservedHold => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('daily_rate')
                    ->label(__('filament.Daily Rate'))
                    ->formatStateUsing(fn ($state): ?string => self::rupiah($state))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('filament.Type'))
                    ->options(self::vehicleTypeOptions()),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.Status'))
                    ->options(self::vehicleStatusOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('filament.Edit')),
                Tables\Actions\Action::make('set_maintenance')
                    ->label(__('filament.Set To Maintenance'))
                    ->icon('heroicon-m-wrench-screwdriver')
                    ->color('warning')
                    ->visible(fn (Vehicle $record): bool => in_array($record->status->value, [VehicleStatus::Available->value, VehicleStatus::ReservedHold->value], true))
                    ->action(fn (Vehicle $record) => self::changeStatus($record, VehicleStatus::Maintenance)),
                Tables\Actions\Action::make('release_to_available')
                    ->label(__('filament.Release'))
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (Vehicle $record): bool => in_array($record->status->value, [VehicleStatus::Maintenance->value, VehicleStatus::OutOfService->value, VehicleStatus::ReservedHold->value], true))
                    ->action(fn (Vehicle $record) => self::changeStatus($record, VehicleStatus::Available)),
                Tables\Actions\Action::make('mark_out_of_service')
                    ->label(__('filament.Mark Out Of Service'))
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn (Vehicle $record): bool => in_array($record->status->value, [VehicleStatus::Available->value, VehicleStatus::Maintenance->value, VehicleStatus::ReservedHold->value], true))
                    ->action(fn (Vehicle $record) => self::changeStatus($record, VehicleStatus::OutOfService)),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }

    /**
     * Build the type-conditional spec fields (car/suv vs motorcycle).
     * Each field writes into the `attributes_json` JSON column and only
     * dehydrates when the selected vehicle type matches its group.
     */
    private static function specFields(): array
    {
        $optionLabels = [
            'manual' => __('filament.Manual'),
            'automatic' => __('filament.Automatic'),
            'scooter' => __('filament.Scooter'),
            'bensin' => __('filament.Petrol'),
            'diesel' => __('filament.Diesel'),
            'hybrid' => __('filament.Hybrid'),
            'listrik' => __('filament.Electric'),
        ];

        $carTypes = [VehicleType::Car->value, VehicleType::Suv->value];
        $motorcycleTypes = [VehicleType::Motorcycle->value];

        $build = function (array $specs, array $types) use ($optionLabels): array {
            $fields = [];

            foreach ($specs as $key => $config) {
                $field = match ($config['type']) {
                    'integer' => Forms\Components\TextInput::make("attributes_json.{$key}")
                        ->label($config['label'])
                        ->numeric(),
                    'enum' => Forms\Components\Select::make("attributes_json.{$key}")
                        ->label($config['label'])
                        ->options(array_combine(
                            $config['options'],
                            array_map(fn (string $option): string => $optionLabels[$option] ?? ucfirst($option), $config['options']),
                        )),
                    'boolean' => Forms\Components\Toggle::make("attributes_json.{$key}")
                        ->label($config['label']),
                    default => Forms\Components\TextInput::make("attributes_json.{$key}")
                        ->label($config['label']),
                };

                $fields[] = $field
                    ->visible(fn (Get $get): bool => in_array($get('type'), $types, true))
                    ->dehydrated(fn (Get $get): bool => in_array($get('type'), $types, true));
            }

            return $fields;
        };

        return [
            ...$build(VehicleType::Car->specFields(), $carTypes),
            ...$build(VehicleType::Motorcycle->specFields(), $motorcycleTypes),
        ];
    }

    private static function changeStatus(Vehicle $vehicle, VehicleStatus $status): void
    {
        $vehicle->update(['status' => $status]);

        Notification::make()
            ->title(__('filament.Status Updated To', ['status' => $status->label()]))
            ->success()
            ->send();
    }

    private static function vehicleTypeOptions(): array
    {
        $options = [];

        foreach (VehicleType::cases() as $type) {
            $options[$type->value] = $type->labelId();
        }

        return $options;
    }

    private static function vehicleStatusOptions(): array
    {
        $options = [];

        foreach (VehicleStatus::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }

    /** on_rent is auto-derived from confirmed bookings, so it is not manually selectable. */
    private static function editableStatusOptions(): array
    {
        return array_filter(
            self::vehicleStatusOptions(),
            fn (string $key): bool => $key !== VehicleStatus::OnRent->value,
            ARRAY_FILTER_USE_KEY,
        );
    }

    private static function enumValue(mixed $state): string
    {
        return $state instanceof \BackedEnum ? $state->value : (string) $state;
    }

    private static function rupiah(mixed $amount): ?string
    {
        if (blank($amount)) {
            return null;
        }

        return 'Rp ' . number_format((int) $amount, 0, ',', '.');
    }
}