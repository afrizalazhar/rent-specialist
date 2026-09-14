<?php

namespace App\Http\Controllers;

use App\Enums\VehicleType;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('tipe');
        $typeEnum = $type ? VehicleType::tryFrom($type) : null;

        $vehicles = Vehicle::query()
            ->publiclyListable()
            ->when($typeEnum, fn ($q) => $q->ofType($typeEnum))
            ->paginate(12)
            ->withQueryString();

        return view('vehicles.index', [
            'vehicles' => $vehicles,
            'activeType' => $typeEnum,
            'typeCounts' => $this->countsByType(),
        ]);
    }

    public function show(Vehicle $vehicle): View
    {
        abort_unless($vehicle->status->isPubliclyVisible(), 404);

        $vehicle->load('branch');

        return view('vehicles.show', [
            'vehicle' => $vehicle,
        ]);
    }

    /** @return array<string,int> */
    private function countsByType(): array
    {
        return Vehicle::query()
            ->publiclyListable()
            ->selectRaw('type, count(*) as c')
            ->groupBy('type')
            ->pluck('c', 'type')
            ->all();
    }
}
