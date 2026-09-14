<?php

namespace App\Filament\Resources\StaffUserResource\Pages;

use App\Filament\Resources\StaffUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaffUser extends CreateRecord
{
    protected static string $resource = StaffUserResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label(__('filament.Create')),
            $this->getCancelFormAction()->label(__('filament.Cancel')),
        ];
    }
}