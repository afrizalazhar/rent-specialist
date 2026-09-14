<?php

namespace App\Services\Pricing;

use App\Models\Vehicle;
use Carbon\CarbonImmutable;

/**
 * Pure pricing math for a rental. No I/O, no DB, no side effects.
 *
 * Rules (see SPEC.md):
 *   - 12-hour half-day floor. Anything < 12h is billed as 12h.
 *   - 12–24h billed as 1 day.
 *   - 7+ days: weekly rate is the per-week price; 14+ days = 2 weeks, etc.
 *   - 30+ days: monthly rate; 60+ days = 2 months, etc.
 *   - Overage: hours past planned return × (daily_rate / 24), rounded up.
 *   - Money is in whole rupiah (integer).
 */
final class PricingCalculator
{
    public const HALF_DAY_HOURS = 12;
    public const HOURS_PER_DAY = 24;
    public const DAYS_PER_WEEK = 7;
    public const DAYS_PER_MONTH = 30;

    /**
     * @param CarbonImmutable $start  Booking start (planned pickup).
     * @param CarbonImmutable $end    Booking end (planned return).
     * @param CarbonImmutable|null $actualEnd Actual return time. Null = no overage yet.
     */
    public function calculate(
        Vehicle $vehicle,
        CarbonImmutable $start,
        CarbonImmutable $end,
        ?CarbonImmutable $actualEnd = null,
    ): PricingResult {
        $end = $end->greaterThan($start) ? $end : $start->addHours(self::HALF_DAY_HOURS);

        $rawHours = $this->hoursBetween($start, $end);
        $billableHours = max(self::HALF_DAY_HOURS, $rawHours);
        $billableDays = max(1, (int) ceil($billableHours / self::HOURS_PER_DAY));

        $baseAmount = $this->baseAmount($vehicle, $billableDays);

        $overageAmount = 0;
        if ($actualEnd !== null && $actualEnd->greaterThan($end)) {
            $overageAmount = $this->overageAmount($vehicle, $end, $actualEnd);
        }

        return new PricingResult(
            durationHours: $rawHours,
            billableHours: $billableHours,
            billableDays: $billableDays,
            baseAmount: $baseAmount,
            overageAmount: $overageAmount,
            totalAmount: $baseAmount + $overageAmount,
        );
    }

    /** Just the base rental amount for a given day count. */
    public function baseAmount(Vehicle $vehicle, int $days): int
    {
        $days = max(1, $days);

        if ($days >= self::DAYS_PER_MONTH && $vehicle->monthly_rate !== null) {
            $months = (int) ceil($days / self::DAYS_PER_MONTH);
            return $months * (int) $vehicle->monthly_rate;
        }

        if ($days >= self::DAYS_PER_WEEK && $vehicle->weekly_rate !== null) {
            $weeks = (int) ceil($days / self::DAYS_PER_WEEK);
            return $weeks * (int) $vehicle->weekly_rate;
        }

        return $days * (int) $vehicle->daily_rate;
    }

    /** Overage charge in whole rupiah, rounded up to the hour. */
    public function overageAmount(
        Vehicle $vehicle,
        CarbonImmutable $plannedReturn,
        CarbonImmutable $actualReturn,
    ): int {
        if (! $actualReturn->greaterThan($plannedReturn)) {
            return 0;
        }

        $minutes = $plannedReturn->diffInMinutes($actualReturn, false);
        $hours = (int) ceil(max(1, $minutes) / 60);

        $hourlyRate = intdiv((int) $vehicle->daily_rate, self::HOURS_PER_DAY);
        if ($hourlyRate <= 0) {
            $hourlyRate = 1;
        }

        return $hours * $hourlyRate;
    }

    private function hoursBetween(CarbonImmutable $start, CarbonImmutable $end): float
    {
        $minutes = $start->diffInMinutes($end, false);
        return max(0.0, $minutes / 60);
    }
}
