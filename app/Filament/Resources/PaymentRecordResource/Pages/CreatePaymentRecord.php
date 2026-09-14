<?php

namespace App\Filament\Resources\PaymentRecordResource\Pages;

use App\Filament\Resources\PaymentRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentRecord extends CreateRecord
{
    protected static string $resource = PaymentRecordResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label(__('filament.Create')),
            $this->getCancelFormAction()->label(__('filament.Cancel')),
        ];
    }
}