<?php

use App\Enums\VehicleType;
use App\Models\Vehicle;
use App\Services\Pricing\PricingCalculator;
use App\Services\Pricing\PricingResult;
use Carbon\CarbonImmutable;

function vehicleWithRates(int $daily = 300_000, ?int $weekly = null, ?int $monthly = null): Vehicle
{
    $v = new Vehicle();
    $v->forceFill([
        'daily_rate'   => $daily,
        'weekly_rate'  => $weekly,
        'monthly_rate' => $monthly,
        'type'         => VehicleType::Car,
    ]);
    return $v;
}

it('charges a 12-hour floor for any booking under 12 hours', function () {
    $calc = new PricingCalculator();
    $result = $calc->calculate(
        vehicleWithRates(),
        CarbonImmutable::parse('2026-03-01 09:00'),
        CarbonImmutable::parse('2026-03-01 15:00'), // 6 hours
    );

    expect($result->billableHours)->toBe(12.0)
        ->and($result->billableDays)->toBe(1)
        ->and($result->baseAmount)->toBe(300_000);
});

it('charges 1 day for 12-24 hour bookings', function () {
    $calc = new PricingCalculator();
    $result = $calc->calculate(
        vehicleWithRates(),
        CarbonImmutable::parse('2026-03-01 09:00'),
        CarbonImmutable::parse('2026-03-02 09:00'), // 24 hours
    );

    expect($result->billableDays)->toBe(1)
        ->and($result->baseAmount)->toBe(300_000);
});

it('rounds up partial days to a full day', function () {
    $calc = new PricingCalculator();
    $result = $calc->calculate(
        vehicleWithRates(),
        CarbonImmutable::parse('2026-03-01 09:00'),
        CarbonImmutable::parse('2026-03-03 15:00'), // 2 days 6 hours -> 3 days
    );

    expect($result->billableDays)->toBe(3)
        ->and($result->baseAmount)->toBe(900_000);
});

it('uses daily rate for 1-6 day bookings', function () {
    $calc = new PricingCalculator();
    $result = $calc->calculate(
        vehicleWithRates(daily: 300_000, weekly: 1_500_000, monthly: 5_000_000),
        CarbonImmutable::parse('2026-03-01 09:00'),
        CarbonImmutable::parse('2026-03-04 09:00'), // 3 days
    );

    expect($result->billableDays)->toBe(3)
        ->and($result->baseAmount)->toBe(900_000);
});

it('uses weekly rate for 7+ day bookings', function () {
    $calc = new PricingCalculator();
    $result = $calc->calculate(
        vehicleWithRates(daily: 300_000, weekly: 1_500_000, monthly: 5_000_000),
        CarbonImmutable::parse('2026-03-01 09:00'),
        CarbonImmutable::parse('2026-03-08 09:00'), // 7 days -> 1 week
    );

    expect($result->billableDays)->toBe(7)
        ->and($result->baseAmount)->toBe(1_500_000);
});

it('rounds up partial weeks to a full week', function () {
    $calc = new PricingCalculator();
    $result = $calc->calculate(
        vehicleWithRates(daily: 300_000, weekly: 1_500_000, monthly: 5_000_000),
        CarbonImmutable::parse('2026-03-01 09:00'),
        CarbonImmutable::parse('2026-03-15 09:00'), // 14 days -> 2 weeks
    );

    expect($result->billableDays)->toBe(14)
        ->and($result->baseAmount)->toBe(3_000_000);
});

it('uses monthly rate for 30+ day bookings', function () {
    $calc = new PricingCalculator();
    $result = $calc->calculate(
        vehicleWithRates(daily: 300_000, weekly: 1_500_000, monthly: 5_000_000),
        CarbonImmutable::parse('2026-03-01 09:00'),
        CarbonImmutable::parse('2026-04-01 09:00'), // 31 days -> 2 months
    );

    expect($result->billableDays)->toBe(31)
        ->and($result->baseAmount)->toBe(10_000_000);
});

it('falls back to daily when weekly/monthly not set', function () {
    $calc = new PricingCalculator();
    $result = $calc->calculate(
        vehicleWithRates(daily: 300_000, weekly: null, monthly: null),
        CarbonImmutable::parse('2026-03-01 09:00'),
        CarbonImmutable::parse('2026-03-08 09:00'), // 7 days
    );

    expect($result->baseAmount)->toBe(2_100_000); // 7 × 300_000
});

it('calculates overage by the hour for late returns', function () {
    $calc = new PricingCalculator();
    $result = $calc->calculate(
        vehicleWithRates(daily: 240_000), // 10_000/hour
        CarbonImmutable::parse('2026-03-01 09:00'),
        CarbonImmutable::parse('2026-03-02 09:00'),          // planned return
        CarbonImmutable::parse('2026-03-02 11:30'),          // 2.5 hours late
    );

    expect($result->overageAmount)->toBe(30_000); // 3 hours × 10_000
});

it('returns no overage for on-time or early returns', function () {
    $calc = new PricingCalculator();
    $result = $calc->calculate(
        vehicleWithRates(),
        CarbonImmutable::parse('2026-03-01 09:00'),
        CarbonImmutable::parse('2026-03-02 09:00'),
        CarbonImmutable::parse('2026-03-02 08:30'),
    );

    expect($result->overageAmount)->toBe(0);
});

it('returns a PricingResult value object', function () {
    $calc = new PricingCalculator();
    $result = $calc->calculate(
        vehicleWithRates(),
        CarbonImmutable::parse('2026-03-01 09:00'),
        CarbonImmutable::parse('2026-03-02 09:00'),
    );

    expect($result)->toBeInstanceOf(PricingResult::class);
});
