<?php

namespace App\Services;

use App\Models\ApiEndpoint;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApiEndpointService
{
    public function listByProject(Project $project, array $filters = []): LengthAwarePaginator
    {
        $query = $project->endpoints()->with('environment');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['method'])) {
            $query->where('method', $filters['method']);
        }

        return $query->latest()->paginate(15);
    }

    public function create(Project $project, array $data): ApiEndpoint
    {
        return $project->endpoints()->create($data);
    }

    public function find(Project $project, int $id): ApiEndpoint
    {
        return $project->endpoints()->with('environment')->findOrFail($id);
    }

    public function update(ApiEndpoint $endpoint, array $data): ApiEndpoint
    {
        $endpoint->update($data);

        return $endpoint;
    }

    public function delete(ApiEndpoint $endpoint): bool
    {
        return $endpoint->delete();
    }
}
