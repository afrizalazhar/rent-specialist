<?php

namespace App\Enums;

enum VehicleType: string
{
    case Car = 'car';
    case Suv = 'suv';
    case Motorcycle = 'motorcycle';

    public function label(): string
    {
        return match ($this) {
            self::Car => 'Car',
            self::Suv => 'SUV',
            self::Motorcycle => 'Motorcycle',
        };
    }

    public function labelId(): string
    {
        return match ($this) {
            self::Car => 'Mobil',
            self::Suv => 'SUV',
            self::Motorcycle => 'Sepeda Motor',
        };
    }

    /**
     * Spec fields exposed for this vehicle type on the public catalog.
     * The "common" field set is added separately (make/model/year/plate/color).
     */
    public function specFields(): array
    {
        return match ($this) {
            self::Car, self::Suv => [
                'seats'       => ['label' => 'Kursi', 'type' => 'integer', 'unit' => null],
                'transmission'=> ['label' => 'Transmisi', 'type' => 'enum', 'options' => ['manual', 'automatic']],
                'fuel'        => ['label' => 'Bahan Bakar', 'type' => 'enum', 'options' => ['bensin', 'diesel', 'hybrid', 'listrik']],
                'doors'       => ['label' => 'Pintu', 'type' => 'integer', 'unit' => null],
                'ac'          => ['label' => 'AC', 'type' => 'boolean'],
            ],
            self::Motorcycle => [
                'engine_cc'   => ['label' => 'Mesin', 'type' => 'integer', 'unit' => 'cc'],
                'transmission'=> ['label' => 'Transmisi', 'type' => 'enum', 'options' => ['manual', 'automatic', 'scooter']],
                'helmet'      => ['label' => 'Helm Termasuk', 'type' => 'boolean'],
            ],
        };
    }
}
