<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReleaseRequest;
use App\Http\Requests\UpdateReleaseRequest;
use App\Http\Resources\ReleaseResource;
use App\Models\Project;
use App\Models\Release;
use App\Services\ReleaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReleaseController extends Controller
{
    public function __construct(
        private readonly ReleaseService $service,
    ) {}

    public function index(Project $project, Request $request): AnonymousResourceCollection
    {
        $releases = $this->service->listByProject($project, $request->only(['status']));

        return ReleaseResource::collection($releases);
    }

    public function store(StoreReleaseRequest $request, Project $project): ReleaseResource
    {
        $release = $this->service->create($project, $request->validated());

        return new ReleaseResource($release);
    }

    public function show(Project $project, Release $release): ReleaseResource
    {
        $release = $this->service->find($project, $release->id);

        return new ReleaseResource($release);
    }

    public function update(UpdateReleaseRequest $request, Project $project, Release $release): ReleaseResource
    {
        $release = $this->service->update($release, $request->validated());

        return new ReleaseResource($release);
    }

    public function destroy(Project $project, Release $release): JsonResponse
    {
        $this->service->delete($release);

        return response()->json(null, 204);
    }
}
