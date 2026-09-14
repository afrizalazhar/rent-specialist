<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $booking_id
 * @property int $amount
 * @property \Illuminate\Support\Carbon $received_at
 * @property PaymentMethod $method
 * @property string|null $reference
 * @property string|null $notes
 * @property int $recorded_by
 */
class PaymentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'received_at',
        'method',
        'reference',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'received_at' => 'datetime',
            'method' => PaymentMethod::class,
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(StaffUser::class, 'recorded_by');
    }
}
