<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiEndpointResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'environment_id' => $this->environment_id,
            'name' => $this->name,
            'method' => $this->method?->value,
            'url' => $this->url,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'last_checked_at' => $this->last_checked_at?->toIso8601String(),
            'response_time_ms' => $this->response_time_ms,
            'environment' => new EnvironmentResource($this->whenLoaded('environment')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
