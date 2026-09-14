<?php

namespace App\Services\Pricing;

/**
 * Value object returned by PricingCalculator.
 *
 * All amounts are integers in the smallest currency unit (IDR = 1 rupiah = 1 unit).
 */
final class PricingResult
{
    public function __construct(
        public readonly float $durationHours,
        public readonly float $billableHours,
        public readonly int $billableDays,
        public readonly int $baseAmount,
        public readonly int $overageAmount,
        public readonly int $totalAmount,
    ) {}

    public function withOverage(int $overage): self
    {
        return new self(
            $this->durationHours,
            $this->billableHours,
            $this->billableDays,
            $this->baseAmount,
            $overage,
            $this->baseAmount + $overage,
        );
    }
}
