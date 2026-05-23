<?php

namespace Src\Presentation\Admin\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'lat'  => (float) $this->lat,
            'lng'  => (float) $this->lng,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
