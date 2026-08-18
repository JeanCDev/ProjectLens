<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Project::with(['environments', 'releases', 'teamMembers.user']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['language'])) {
            $query->where('primary_language', $filters['language']);
        }

        return $query->latest()->paginate(15);
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function find(int $id): Project
    {
        return Project::with(['environments', 'releases', 'endpoints', 'teamMembers.user'])->findOrFail($id);
    }

    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        return $project->fresh(['environments', 'releases', 'endpoints', 'teamMembers.user']);
    }

    public function delete(Project $project): bool
    {
        return $project->delete();
    }

    public function byStatus(ProjectStatus $status): LengthAwarePaginator
    {
        return Project::where('status', $status)
            ->with(['environments', 'releases'])
            ->latest()
            ->paginate(15);
    }
}
