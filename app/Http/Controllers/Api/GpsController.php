<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrder;
use App\Models\GpsLog;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class GpsController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('driver')) {
            // Cache vehicle ID per driver — refreshes every 10 min or on vehicle change
            $vehicleId = cache()->remember(
                "driver_vehicle_id_{$user->id}",
                600,
                fn () => Vehicle::where('driver_id', $user->id)->value('id')
            );
            if (!$vehicleId) {
                return response()->json(['message' => 'ไม่พบยานพาหนะที่ผูกกับบัญชีนี้'], 422);
            }
            $request->validate([
                'lat' => 'required|numeric|between:-90,90',
                'lng' => 'required|numeric|between:-180,180',
            ]);
            $deliveryOrderId = DeliveryOrder::where('vehicle_id', $vehicleId)
                ->where('status', 'in_transit')
                ->value('id');
        } else {
            $request->validate([
                'vehicle_id' => 'required|exists:vehicles,id',
                'delivery_order_id' => 'nullable|exists:delivery_orders,id',
                'lat' => 'required|numeric|between:-90,90',
                'lng' => 'required|numeric|between:-180,180',
            ]);
            $vehicleId = $request->vehicle_id;
            $deliveryOrderId = $request->delivery_order_id;
        }

        $log = GpsLog::create([
            'vehicle_id' => $vehicleId,
            'delivery_order_id' => $deliveryOrderId ?? null,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'recorded_at' => now(),
        ]);

        return response()->json($log, 201);
    }

    public function history(Vehicle $vehicle)
    {
        $logs = $vehicle->gpsLogs()
            ->with('deliveryOrder')
            ->latest('recorded_at')
            ->limit(100)
            ->get();

        return response()->json($logs);
    }

    public function getDistance(int $vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);
        if (!$vehicle) {
            return response()->json(['message' => 'ไม่พบยานพาหนะ'], 404);
        }

        $latestLog = GpsLog::where('vehicle_id', $vehicleId)
            ->latest('recorded_at')
            ->first();

        if (!$latestLog) {
            return response()->json(['message' => 'ไม่มีข้อมูล GPS'], 404);
        }

        $do = DeliveryOrder::where('status', 'in_transit')
            ->where('vehicle_id', $vehicleId)
            ->with('branch')
            ->first();

        if (!$do || !$do->branch || !$do->branch->lat || !$do->branch->lng) {
            return response()->json(['message' => 'ไม่พบ DO ที่กำลังส่งหรือพิกัดสาขา'], 404);
        }

        $distanceKm = $this->haversine(
            (float) $latestLog->lat, (float) $latestLog->lng,
            (float) $do->branch->lat, (float) $do->branch->lng
        );

        return response()->json([
            'vehicle_id'    => $vehicleId,
            'license_plate' => $vehicle->license_plate,
            'do_number'     => $do->do_number,
            'branch_name'   => $do->branch->name,
            'branch_lat'    => $do->branch->lat,
            'branch_lng'    => $do->branch->lng,
            'distance_km'   => round($distanceKm, 1),
            'eta_minutes'   => (int) round($distanceKm / 40 * 60),
            'last_gps_at'   => $latestLog->recorded_at,
        ]);
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
