<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Parking;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\ParkingRequest;
use App\Services\ParkingPriceService;
use App\Http\Resources\ParkingResource;

class ParkingController extends Controller
{

    public function index()
    {
        $data = Parking::with('vehicle')->whereNull('stop_time')->get();
        return ParkingResource::collection($data);
    }

    public function start(ParkingRequest $request)
    {
        $data = $request->validated();

        $active = Parking::active()->where('vehicle_id', $data['vehicle_id'])->first();

        if($active)
        {
            return response()->json([
                'errors' => ['general' => ['Tidak bisa melakukan parkir dua kali untuk kendaraan yang sama. Mohon keluarkan terlebih dahulu parkir sebelumnya']]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $parking = Parking::create($data);
        $parking->load('vehicle', 'zone');

        return ParkingResource::make($parking);
    }

    public function show(Parking $parking)
    {
        return ParkingResource::make($parking);
    }

    public function stop(Parking $parking, ParkingPriceService $parkingPriceService)
    {
        $parking->update([
            'stop_time' => now(),
            'total_price' => $parkingPriceService->calculatePrice(zone_id: $parking->zone_id,
            startTime:$parking->start_time, stopTime: $parking->stop_time)
        ]);

        return ParkingResource::make($parking);
    }

    public function history()
    {
        $data = Parking::with(['vehicle' => fn($q) => $q->withTrashed(),'zone'])->stopped()->get();

        return ParkingResource::collection($data);
    }
}
