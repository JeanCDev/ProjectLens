<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Release;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReleaseService
{
    public function listByProject(Project $project, array $filters = []): LengthAwarePaginator
    {
        $query = $project->releases();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest('released_at')->paginate(15);
    }

    public function create(Project $project, array $data): Release
    {
        return $project->releases()->create($data);
    }

    public function find(Project $project, int $id): Release
    {
        return $project->releases()->findOrFail($id);
    }

    public function update(Release $release, array $data): Release
    {
        $release->update($data);

        return $release;
    }

    public function delete(Release $release): bool
    {
        return $release->delete();
    }
}
