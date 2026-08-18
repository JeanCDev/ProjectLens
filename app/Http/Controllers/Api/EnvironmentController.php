<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnvironmentRequest;
use App\Http\Requests\UpdateEnvironmentRequest;
use App\Http\Resources\EnvironmentResource;
use App\Models\Environment;
use App\Models\Project;
use App\Services\EnvironmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EnvironmentController extends Controller
{
    public function __construct(
        private readonly EnvironmentService $service,
    ) {}

    public function index(Project $project, Request $request): AnonymousResourceCollection
    {
        $environments = $this->service->listByProject($project);

        return EnvironmentResource::collection($environments);
    }

    public function store(StoreEnvironmentRequest $request, Project $project): EnvironmentResource
    {
        $environment = $this->service->create($project, $request->validated());

        return new EnvironmentResource($environment);
    }

    public function show(Project $project, Environment $environment): EnvironmentResource
    {
        $environment = $this->service->find($project, $environment->id);

        return new EnvironmentResource($environment);
    }

    public function update(UpdateEnvironmentRequest $request, Project $project, Environment $environment): EnvironmentResource
    {
        $environment = $this->service->update($environment, $request->validated());

        return new EnvironmentResource($environment);
    }

    public function destroy(Project $project, Environment $environment): JsonResponse
    {
        $this->service->delete($environment);

        return response()->json(null, 204);
    }
}
