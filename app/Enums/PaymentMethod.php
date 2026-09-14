<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case EWallet = 'e_wallet';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash         => 'Tunai',
            self::BankTransfer => 'Transfer Bank',
            self::EWallet      => 'Dompet Digital',
            self::Other        => 'Lainnya',
        };
    }
}
