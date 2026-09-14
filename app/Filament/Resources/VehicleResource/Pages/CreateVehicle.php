<?php

namespace App\Filament\Resources\VehicleResource\Pages;

use App\Filament\Resources\VehicleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicle extends CreateRecord
{
    protected static string $resource = VehicleResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label(__('filament.Create')),
            $this->getCancelFormAction()->label(__('filament.Cancel')),
        ];
    }
}