<?php

namespace App\Filament\Resources;

use App\Enums\PaymentMethod;
use App\Filament\Resources\PaymentRecordResource\Pages;
use App\Models\Booking;
use App\Models\PaymentRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentRecordResource extends Resource
{
    protected static ?string $model = PaymentRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getModelLabel(): string
    {
        return __('filament.Payment Record');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.Payment Records');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.Operasional');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('booking_id')
                            ->label(__('filament.Booking'))
                            ->relationship('booking', 'id')
                            ->getOptionLabelFromRecordUsing(fn (Booking $record): string => '#' . $record->id . ' · ' . ($record->customer?->name ?? '—') . ' · ' . ($record->vehicle?->displayName() ?? '—'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label(__('filament.Amount'))
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\DateTimePicker::make('received_at')
                            ->label(__('filament.Received At'))
                            ->seconds(false)
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('method')
                            ->label(__('filament.Method'))
                            ->options(self::paymentMethodOptions())
                            ->required(),
                        Forms\Components\TextInput::make('reference')
                            ->label(__('filament.Reference'))
                            ->maxLength(100),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('filament.Notes'))
                            ->columnSpanFull(),
                        Forms\Components\Select::make('recorded_by')
                            ->label(__('filament.Recorded By'))
                            ->relationship('recorder', 'name')
                            ->default(fn (): ?int => auth()->id())
                            ->hidden(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by'] = auth()->id();

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_id')
                    ->label(__('filament.Booking'))
                    ->getStateUsing(fn (PaymentRecord $record): string => '#' . $record->booking_id . ' · ' . ($record->booking?->customer?->name ?? '—'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('filament.Amount'))
                    ->formatStateUsing(fn ($state): ?string => self::rupiah($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('method')
                    ->label(__('filament.Method'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => PaymentMethod::from(self::enumValue($state))->label()),
                Tables\Columns\TextColumn::make('received_at')
                    ->label(__('filament.Received At'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('recorder_name')
                    ->label(__('filament.Recorded By'))
                    ->getStateUsing(fn (PaymentRecord $record): ?string => $record->recorder?->name),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('method')
                    ->label(__('filament.Method'))
                    ->options(self::paymentMethodOptions()),
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
            ->defaultSort('received_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentRecords::route('/'),
            'create' => Pages\CreatePaymentRecord::route('/create'),
            'edit' => Pages\EditPaymentRecord::route('/{record}/edit'),
        ];
    }

    private static function paymentMethodOptions(): array
    {
        $options = [];

        foreach (PaymentMethod::cases() as $method) {
            $options[$method->value] = $method->label();
        }

        return $options;
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