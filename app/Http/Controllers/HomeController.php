<?php

namespace App\Http\Controllers;

use App\Enums\VehicleType;
use App\Models\Branch;
use App\Models\PromoSlide;
use App\Models\Vehicle;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $branch = Branch::first();

        $featured = Vehicle::query()
            ->publiclyListable()
            ->take(6)
            ->get();

        // Spotlight prefers an SUV (Innova Reborn-style family vehicle) but
        // falls back to whatever is available so the section never disappears.
        $spotlight = $featured->firstWhere('type', VehicleType::Car->value)
            ?? $featured->first();

        // Available units for the hero availability form. Capped to a small
        // list — the hero shouldn't overwhelm the visitor with choices.
        $units = Vehicle::query()
            ->publiclyListable()
            ->orderBy('daily_rate')
            ->get(['id', 'make', 'model', 'year', 'type', 'daily_rate']);

        // Service areas — Jakarta + satellite cities, with the branch first
        // when it has a known location.
        $locations = collect(['Jakarta', 'Jakarta Selatan', 'Tangerang', 'Bekasi', 'Depok', 'Bogor'])
            ->unique()
            ->values();

        // Active promo slides, ordered for the slider.
        $promoSlides = PromoSlide::query()
            ->active()
            ->get();

        return view('home', [
            'branch' => $branch,
            'featured' => $featured,
            'spotlight' => $spotlight,
            'units' => $units,
            'locations' => $locations,
            'promoSlides' => $promoSlides,
        ]);
    }
}
