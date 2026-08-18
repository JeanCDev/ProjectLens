<?php

namespace App\Services;

use App\Models\Project;
use App\Models\TeamMember;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TeamMemberService
{
    public function listByProject(Project $project): LengthAwarePaginator
    {
        return $project->teamMembers()->with('user')->latest()->paginate(15);
    }

    public function create(Project $project, array $data): TeamMember
    {
        return $project->teamMembers()->create($data)->load('user');
    }

    public function find(Project $project, int $id): TeamMember
    {
        return $project->teamMembers()->with('user')->findOrFail($id);
    }

    public function update(TeamMember $member, array $data): TeamMember
    {
        $member->update($data);

        return $member->load('user');
    }

    public function delete(TeamMember $member): bool
    {
        return $member->delete();
    }
}
