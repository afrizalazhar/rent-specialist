<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string|null $whatsapp
 * @property string|null $address
 * @property string|null $notes
 */
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'whatsapp',
        'address',
        'notes',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class)->latest();
    }

    /** WhatsApp number with country code (digits only), or null. */
    public function whatsappDigits(): ?string
    {
        if (! $this->whatsapp) {
            return null;
        }
        $digits = preg_replace('/\D+/', '', $this->whatsapp) ?? '';
        // Default to +62 (Indonesia) if the number starts with 0.
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }
        return $digits !== '' ? $digits : null;
    }
}
