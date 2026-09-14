<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label(__('filament.Create')),
            $this->getCancelFormAction()->label(__('filament.Cancel')),
        ];
    }
}