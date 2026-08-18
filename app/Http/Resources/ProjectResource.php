<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'primary_language' => $this->primary_language,
            'framework' => $this->framework,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'start_date' => $this->start_date?->toDateString(),
            'estimated_end_date' => $this->estimated_end_date?->toDateString(),
            'git_repository' => $this->git_repository,
            'documentation_url' => $this->documentation_url,
            'environments' => EnvironmentResource::collection($this->whenLoaded('environments')),
            'releases' => ReleaseResource::collection($this->whenLoaded('releases')),
            'endpoints' => ApiEndpointResource::collection($this->whenLoaded('endpoints')),
            'team_members' => TeamMemberResource::collection($this->whenLoaded('teamMembers')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
