<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamMemberRequest;
use App\Http\Requests\UpdateTeamMemberRequest;
use App\Http\Resources\TeamMemberResource;
use App\Models\Project;
use App\Models\TeamMember;
use App\Services\TeamMemberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamMemberController extends Controller
{
    public function __construct(
        private readonly TeamMemberService $service,
    ) {}

    public function index(Project $project, Request $request): AnonymousResourceCollection
    {
        $members = $this->service->listByProject($project);

        return TeamMemberResource::collection($members);
    }

    public function store(StoreTeamMemberRequest $request, Project $project): TeamMemberResource
    {
        $member = $this->service->create($project, $request->validated());

        return new TeamMemberResource($member);
    }

    public function show(Project $project, TeamMember $teamMember): TeamMemberResource
    {
        $member = $this->service->find($project, $teamMember->id);

        return new TeamMemberResource($member);
    }

    public function update(UpdateTeamMemberRequest $request, Project $project, TeamMember $teamMember): TeamMemberResource
    {
        $member = $this->service->update($teamMember, $request->validated());

        return new TeamMemberResource($member);
    }

    public function destroy(Project $project, TeamMember $teamMember): JsonResponse
    {
        $this->service->delete($teamMember);

        return response()->json(null, 204);
    }
}
