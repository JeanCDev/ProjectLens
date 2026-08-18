<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $service,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = $this->service->list($request->only(['status', 'search', 'language']));

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request): ProjectResource
    {
        $project = $this->service->create($request->validated());

        return new ProjectResource($project);
    }

    public function show(Project $project): ProjectResource
    {
        $project = $this->service->find($project->id);

        return new ProjectResource($project);
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $project = $this->service->update($project, $request->validated());

        return new ProjectResource($project);
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->service->delete($project);

        return response()->json(null, 204);
    }
}
