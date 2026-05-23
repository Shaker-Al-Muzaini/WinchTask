<?php

namespace Src\Presentation\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'status'     => $this->status,
            'lat'        => (float) $this->lat,
            'lng'        => (float) $this->lng,
            'driver'     => new DriverResource($this->whenLoaded('driver')), // شحن بيانات السائق إن وجدت كـ Nested Resource
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
