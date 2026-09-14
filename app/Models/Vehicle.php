<?php

namespace App\Models;

use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $branch_id
 * @property VehicleType $type
 * @property VehicleStatus $status
 * @property string $make
 * @property string $model
 * @property int $year
 * @property string $plate_number
 * @property string|null $color
 * @property array<string,mixed> $attributes_json
 * @property array<int,string> $photos
 * @property int $daily_rate
 * @property int|null $weekly_rate
 * @property int|null $monthly_rate
 * @property string|null $notes
 */
class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'type',
        'status',
        'make',
        'model',
        'year',
        'plate_number',
        'color',
        'attributes_json',
        'photos',
        'daily_rate',
        'weekly_rate',
        'monthly_rate',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => VehicleType::class,
            'status' => VehicleStatus::class,
            'year' => 'integer',
            'attributes_json' => 'array',
            'photos' => 'array',
            'daily_rate' => 'integer',
            'weekly_rate' => 'integer',
            'monthly_rate' => 'integer',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** "Toyota Avanza 2022" — used in titles, CTAs, alt text. */
    public function displayName(): string
    {
        return trim("{$this->make} {$this->model} {$this->year}");
    }

    /** "Avanza" — used in WhatsApp prefilled messages and short labels. */
    public function shortName(): string
    {
        return $this->model;
    }

    /** Effective live status, derived from confirmed bookings whose window contains "now". */
    public function effectiveStatus(): VehicleStatus
    {
        if (! in_array($this->status, [VehicleStatus::Available, VehicleStatus::ReservedHold], true)) {
            return $this->status;
        }

        $now = now();
        $onRent = $this->bookings()
            ->where('status', \App\Enums\BookingStatus::Confirmed)
            ->where('planned_pickup_at', '<=', $now)
            ->where('planned_return_at', '>=', $now)
            ->exists();

        return $onRent ? VehicleStatus::OnRent : $this->status;
    }

    /** Type-specific attribute value (e.g. $vehicle->spec('seats')). */
    public function spec(string $key, mixed $default = null): mixed
    {
        return data_get($this->attributes_json, $key, $default);
    }

    public function scopePubliclyListable(Builder $query): Builder
    {
        return $query->whereIn('status', [VehicleStatus::Available->value])
            ->orderByDesc('created_at');
    }

    public function scopeOfType(Builder $query, VehicleType $type): Builder
    {
        return $query->where('type', $type->value);
    }
}
