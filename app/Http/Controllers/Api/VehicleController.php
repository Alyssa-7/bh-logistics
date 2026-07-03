<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        return response()->json(Vehicle::with('driver')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'license_plate' => 'required|string|unique:vehicles,license_plate,NULL,id,deleted_at,NULL',
            'driver_id' => 'nullable|exists:users,id',
        ]);

        $vehicle = Vehicle::create($request->only(['license_plate', 'driver_id']));

        return response()->json($vehicle->load('driver'), 201);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'license_plate' => 'sometimes|string|unique:vehicles,license_plate,' . $vehicle->id,
            'driver_id' => 'sometimes|nullable|exists:users,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $vehicle->update($request->only(['license_plate', 'driver_id', 'is_active']));

        return response()->json($vehicle->load('driver'));
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return response()->json(['message' => 'Vehicle deleted successfully.']);
    }
}
