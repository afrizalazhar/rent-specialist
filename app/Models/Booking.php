<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Services\Pricing\PricingCalculator;
use App\Services\Pricing\PricingResult;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $vehicle_id
 * @property int $branch_id
 * @property BookingStatus $status
 * @property \Illuminate\Support\Carbon $planned_pickup_at
 * @property \Illuminate\Support\Carbon $planned_return_at
 * @property \Illuminate\Support\Carbon|null $actual_pickup_at
 * @property \Illuminate\Support\Carbon|null $actual_return_at
 * @property int $calculated_base_amount
 * @property int $calculated_overage_amount
 * @property int $charged_amount
 * @property string|null $notes
 */
class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'vehicle_id',
        'branch_id',
        'status',
        'planned_pickup_at',
        'planned_return_at',
        'actual_pickup_at',
        'actual_return_at',
        'calculated_base_amount',
        'calculated_overage_amount',
        'charged_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'planned_pickup_at' => 'datetime',
            'planned_return_at' => 'datetime',
            'actual_pickup_at' => 'datetime',
            'actual_return_at' => 'datetime',
            'calculated_base_amount' => 'integer',
            'calculated_overage_amount' => 'integer',
            'charged_amount' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function paymentRecords(): HasMany
    {
        return $this->hasMany(PaymentRecord::class);
    }

    /** Sum of all payment records attached to this booking. */
    public function paidAmount(): int
    {
        return (int) $this->paymentRecords()->sum('amount');
    }

    public function outstandingAmount(): int
    {
        return max(0, $this->charged_amount - $this->paidAmount());
    }

    public function isPaidInFull(): bool
    {
        return $this->outstandingAmount() === 0;
    }

    /** Returns the [start, end) window as CarbonImmutable for pricing math. */
    public function window(): array
    {
        return [
            CarbonImmutable::instance($this->planned_pickup_at),
            CarbonImmutable::instance($this->planned_return_at),
        ];
    }

    /**
     * Recalculate base + overage from the vehicle's current rates and the
     * booking's planned window + actual return (if any). Does not touch
     * `charged_amount` (that's the staff's final, possibly overridden, number).
     */
    public function recalculateAmounts(PricingCalculator $calc): PricingResult
    {
        [$start, $end] = $this->window();
        $actual = $this->actual_return_at
            ? CarbonImmutable::instance($this->actual_return_at)
            : null;

        return $calc->calculate($this->vehicle, $start, $end, $actual);
    }

    public function scopeOverlapping(Builder $query, $start, $end): Builder
    {
        return $query->where('planned_pickup_at', '<', $end)
            ->where('planned_return_at', '>', $start);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            BookingStatus::Confirmed->value,
            BookingStatus::PickedUp->value,
        ]);
    }
}
