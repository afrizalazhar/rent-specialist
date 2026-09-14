<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label(__('filament.Create')),
            $this->getCancelFormAction()->label(__('filament.Cancel')),
        ];
    }
}