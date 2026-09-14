<?php

namespace App\Filament\Resources\PaymentRecordResource\Pages;

use App\Filament\Resources\PaymentRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentRecord extends EditRecord
{
    protected static string $resource = PaymentRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label(__('filament.Delete')),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label(__('filament.Save')),
            $this->getCancelFormAction()->label(__('filament.Cancel')),
        ];
    }
}