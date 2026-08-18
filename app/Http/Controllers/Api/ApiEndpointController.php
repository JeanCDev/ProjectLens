<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApiEndpointRequest;
use App\Http\Requests\UpdateApiEndpointRequest;
use App\Http\Resources\ApiEndpointResource;
use App\Models\ApiEndpoint;
use App\Models\Project;
use App\Services\ApiEndpointService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ApiEndpointController extends Controller
{
    public function __construct(
        private readonly ApiEndpointService $service,
    ) {}

    public function index(Project $project, Request $request): AnonymousResourceCollection
    {
        $endpoints = $this->service->listByProject($project, $request->only(['status', 'method']));

        return ApiEndpointResource::collection($endpoints);
    }

    public function store(StoreApiEndpointRequest $request, Project $project): ApiEndpointResource
    {
        $endpoint = $this->service->create($project, $request->validated());

        return new ApiEndpointResource($endpoint);
    }

    public function show(Project $project, ApiEndpoint $endpoint): ApiEndpointResource
    {
        $endpoint = $this->service->find($project, $endpoint->id);

        return new ApiEndpointResource($endpoint);
    }

    public function update(UpdateApiEndpointRequest $request, Project $project, ApiEndpoint $endpoint): ApiEndpointResource
    {
        $endpoint = $this->service->update($endpoint, $request->validated());

        return new ApiEndpointResource($endpoint);
    }

    public function destroy(Project $project, ApiEndpoint $endpoint): JsonResponse
    {
        $this->service->delete($endpoint);

        return response()->json(null, 204);
    }
}
