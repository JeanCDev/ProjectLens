<?php

namespace App\Policies;

use App\Models\ApiEndpoint;
use App\Models\User;

class ApiEndpointPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ApiEndpoint $apiEndpoint): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ApiEndpoint $apiEndpoint): bool
    {
        return true;
    }

    public function delete(User $user, ApiEndpoint $apiEndpoint): bool
    {
        return true;
    }
}
