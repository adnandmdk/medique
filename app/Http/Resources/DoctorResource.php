<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ClinicResource;

class DoctorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->user->name,
            'email'          => $this->user->email,
            'phone'          => $this->user->phone,
            'specialization' => $this->specialization,
            'licence_number' => $this->licence_number,
            'clinic'         => new ClinicResource($this->whenLoaded('clinic')),
        ];
    }
}