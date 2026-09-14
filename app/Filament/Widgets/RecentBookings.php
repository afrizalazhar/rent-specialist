<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentBookings extends BaseWidget
{
    protected static ?int $sort = 3;

    /**
     * Full width on mobile, half on md+ so it sits beside RevenueChart.
     */
    protected int | string | array $columnSpan = ['default' => 'full', 'md' => 1, 'xl' => 1];

    protected function getTableHeading(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return __('filament.Recent Bookings');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->with(['customer', 'vehicle'])
                    ->latest()
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('vehicle')
                    ->label(__('filament.Vehicle'))
                    ->getStateUsing(fn (Booking $record): ?string => $record->vehicle?->displayName())
                    ->searchable(false)
                    ->sortable(false),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__('filament.Customer'))
                    ->searchable(false)
                    ->sortable(false),

                Tables\Columns\TextColumn::make('planned_pickup_at')
                    ->label(__('filament.Planned Pickup'))
                    ->dateTime('d M Y H:i')
                    ->sortable(false),

                Tables\Columns\TextColumn::make('planned_return_at')
                    ->label(__('filament.Planned Return'))
                    ->dateTime('d M Y H:i')
                    ->sortable(false),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.Status'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state instanceof BookingStatus ? $state->label() : BookingStatus::from((string) $state)->label())
                    ->color(fn ($state): string => match ($state instanceof BookingStatus ? $state : BookingStatus::from((string) $state)) {
                        BookingStatus::Draft => 'gray',
                        BookingStatus::Confirmed => 'info',
                        BookingStatus::PickedUp => 'warning',
                        BookingStatus::Returned => 'warning',
                        BookingStatus::Closed => 'success',
                        BookingStatus::Cancelled => 'danger',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label(__('filament.View'))
                    ->icon('heroicon-m-eye')
                    ->url(fn (Booking $record): string => route('filament.admin.resources.bookings.edit', ['record' => $record])),
            ])
            ->headerActions([
                Tables\Actions\Action::make('all')
                    ->label(__('filament.View All Bookings'))
                    ->url(route('filament.admin.resources.bookings.index'))
                    ->icon('heroicon-m-arrow-right')
                    ->color('gray'),
            ]);
    }
}
