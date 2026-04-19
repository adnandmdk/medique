<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QueueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'queue_number' => $this->queue_number,
            'booking_date' => $this->booking_date->format('Y-m-d'),
            'status'       => $this->status,
            'status_label' => $this->status_label,
            'token'        => $this->token,
            'patient'      => new UserResource($this->whenLoaded('patient')),
            'schedule'     => new ScheduleResource($this->whenLoaded('schedule')),
            'created_at'   => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}