<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClinicResource;
use App\Models\Clinic;
use Illuminate\Http\JsonResponse;

class ClinicController extends Controller
{
    // GET /api/clinics
    public function index(): JsonResponse
    {
        $clinics = Clinic::active()->get();

        return response()->json([
            'data' => ClinicResource::collection($clinics),
        ]);
    }

    // GET /api/clinics/{clinic}
    public function show(Clinic $clinic): JsonResponse
    {
        return response()->json([
            'data' => new ClinicResource($clinic),
        ]);
    }
}