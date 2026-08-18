<?php

namespace App\Services;

use App\Models\Environment;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EnvironmentService
{
    public function listByProject(Project $project): LengthAwarePaginator
    {
        return $project->environments()->latest()->paginate(15);
    }

    public function create(Project $project, array $data): Environment
    {
        return $project->environments()->create($data);
    }

    public function find(Project $project, int $id): Environment
    {
        return $project->environments()->findOrFail($id);
    }

    public function update(Environment $environment, array $data): Environment
    {
        $environment->update($data);

        return $environment;
    }

    public function delete(Environment $environment): bool
    {
        return $environment->delete();
    }
}
