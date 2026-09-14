<?php

namespace App\Filament\Resources;

use App\Enums\BookingStatus;
use App\Enums\VehicleStatus;
use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\Vehicle;
use App\Services\Pricing\PricingCalculator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function getModelLabel(): string
    {
        return __('filament.Booking');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.Bookings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.Operasional');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.Booking Details'))
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label(__('filament.Customer'))
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('vehicle_id')
                            ->label(__('filament.Vehicle'))
                            ->options(fn (?Booking $record): array => self::vehicleOptions($record))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('branch_id', Vehicle::find($state)?->branch_id)),
                        Forms\Components\Select::make('branch_id')
                            ->label(__('filament.Branch'))
                            ->options(fn (): array => Branch::query()->pluck('name', 'id')->all())
                            ->default(fn (): ?int => Branch::query()->value('id'))
                            ->hidden(),
                        Forms\Components\Select::make('status')
                            ->label(__('filament.Status'))
                            ->options(self::bookingStatusOptions())
                            ->default(BookingStatus::Draft->value)
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\DateTimePicker::make('planned_pickup_at')
                            ->label(__('filament.Planned Pickup'))
                            ->seconds(false)
                            ->required(),
                        Forms\Components\DateTimePicker::make('planned_return_at')
                            ->label(__('filament.Planned Return'))
                            ->seconds(false)
                            ->required(),
                        Forms\Components\DateTimePicker::make('actual_pickup_at')
                            ->label(__('filament.Actual Pickup'))
                            ->seconds(false),
                        Forms\Components\DateTimePicker::make('actual_return_at')
                            ->label(__('filament.Actual Return'))
                            ->seconds(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('filament.Pricing'))
                    ->schema([
                        Forms\Components\TextInput::make('calculated_base_amount')
                            ->label(__('filament.Calculated Base'))
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('calculated_overage_amount')
                            ->label(__('filament.Calculated Overage'))
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('charged_amount')
                            ->label(__('filament.Charged Amount'))
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                    ])
                    ->columns(3),

                Forms\Components\Section::make(__('filament.Notes'))
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(__('filament.Notes'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vehicle_display')
                    ->label(__('filament.Vehicle'))
                    ->getStateUsing(fn (Booking $record): ?string => $record->vehicle?->displayName())
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('vehicle', fn (Builder $q): Builder => $q
                            ->where('make', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%")
                            ->orWhere('plate_number', 'like', "%{$search}%"));
                    })
                    ->sortable(['vehicle_id']),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__('filament.Customer'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('planned_pickup_at')
                    ->label(__('filament.Planned Pickup'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('planned_return_at')
                    ->label(__('filament.Planned Return'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.Status'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => BookingStatus::from(self::enumValue($state))->label())
                    ->color(fn ($state): string => match (BookingStatus::from(self::enumValue($state))) {
                        BookingStatus::Draft => 'gray',
                        BookingStatus::Confirmed => 'info',
                        BookingStatus::PickedUp => 'warning',
                        BookingStatus::Returned => 'warning',
                        BookingStatus::Closed => 'success',
                        BookingStatus::Cancelled => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('charged_amount')
                    ->label(__('filament.Charged Amount'))
                    ->formatStateUsing(fn ($state): ?string => self::rupiah($state))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.Status'))
                    ->options(self::bookingStatusOptions()),
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label(__('filament.Vehicle'))
                    ->relationship('vehicle', 'plate_number'),
                Tables\Filters\SelectFilter::make('customer_id')
                    ->label(__('filament.Customer'))
                    ->relationship('customer', 'name'),
                Tables\Filters\Filter::make('planned_window')
                    ->label(__('filament.Planned Window'))
                    ->form([
                        Forms\Components\DatePicker::make('from')->label(__('filament.From')),
                        Forms\Components\DatePicker::make('until')->label(__('filament.Until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $query, string $date): Builder => $query->whereDate('planned_pickup_at', '>=', $date))
                            ->when($data['until'], fn (Builder $query, string $date): Builder => $query->whereDate('planned_pickup_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('filament.Edit')),
                Tables\Actions\Action::make('confirm')
                    ->label(__('filament.Confirm Booking'))
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn (Booking $record): bool => $record->status->canTransitionTo(BookingStatus::Confirmed))
                    ->action(fn (Booking $record) => self::transition($record, BookingStatus::Confirmed)),
                Tables\Actions\Action::make('mark_picked_up')
                    ->label(__('filament.Mark Picked Up'))
                    ->icon('heroicon-m-hand-raised')
                    ->color('primary')
                    ->visible(fn (Booking $record): bool => $record->status->canTransitionTo(BookingStatus::PickedUp))
                    ->action(fn (Booking $record) => self::transition($record, BookingStatus::PickedUp)),
                Tables\Actions\Action::make('mark_returned')
                    ->label(__('filament.Mark Returned'))
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('primary')
                    ->visible(fn (Booking $record): bool => $record->status->canTransitionTo(BookingStatus::Returned))
                    ->action(fn (Booking $record) => self::transition($record, BookingStatus::Returned)),
                Tables\Actions\Action::make('close')
                    ->label(__('filament.Close Booking'))
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->visible(fn (Booking $record): bool => $record->status->canTransitionTo(BookingStatus::Closed))
                    ->action(fn (Booking $record) => self::transition($record, BookingStatus::Closed)),
                Tables\Actions\Action::make('cancel')
                    ->label(__('filament.Cancel Booking'))
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Booking $record): bool => $record->status->canTransitionTo(BookingStatus::Cancelled))
                    ->action(fn (Booking $record) => self::transition($record, BookingStatus::Cancelled)),
                Tables\Actions\Action::make('recalculate')
                    ->label(__('filament.Recalculate'))
                    ->icon('heroicon-m-calculator')
                    ->color('gray')
                    ->action(function (Booking $record) {
                        $result = $record->recalculateAmounts(app(PricingCalculator::class));

                        $record->update([
                            'calculated_base_amount' => $result->baseAmount,
                            'calculated_overage_amount' => $result->overageAmount,
                        ]);

                        Notification::make()
                            ->title(__('filament.Recalculated'))
                            ->body(__('filament.Calculated Total') . ': ' . self::rupiah($result->totalAmount))
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    /** Bookable vehicles only; when editing, the currently assigned vehicle stays selectable. */
    private static function vehicleOptions(?Booking $record = null): array
    {
        $bookable = [VehicleStatus::Available->value, VehicleStatus::ReservedHold->value];

        return Vehicle::query()
            ->whereIn('status', $bookable)
            ->when($record, fn (Builder $query): Builder => $query->orWhere('id', $record->vehicle_id))
            ->get()
            ->mapWithKeys(fn (Vehicle $vehicle): array => [
                $vehicle->id => "{$vehicle->plate_number} · {$vehicle->displayName()}",
            ])
            ->all();
    }

    /** Apply a state change, respecting BookingStatus::canTransitionTo(). */
    private static function transition(Booking $booking, BookingStatus $status): void
    {
        if (! $booking->status->canTransitionTo($status)) {
            Notification::make()
                ->title(__('filament.Invalid Transition'))
                ->danger()
                ->send();

            return;
        }

        $data = ['status' => $status];

        if ($status === BookingStatus::PickedUp && $booking->actual_pickup_at === null) {
            $data['actual_pickup_at'] = now();
        }

        if ($status === BookingStatus::Returned && $booking->actual_return_at === null) {
            $data['actual_return_at'] = now();
        }

        $booking->update($data);

        Notification::make()
            ->title(__('filament.Status Updated To', ['status' => $status->label()]))
            ->success()
            ->send();
    }

    private static function bookingStatusOptions(): array
    {
        $options = [];

        foreach (BookingStatus::cases() as $status) {
            $options[$status->value] = $status->label();
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