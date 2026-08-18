<?php

namespace App\Http\Requests;

use App\Enums\TeamMemberRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'exists:users,id'],
            'role' => ['sometimes', 'string', Rule::enum(TeamMemberRole::class)],
        ];
    }
}
