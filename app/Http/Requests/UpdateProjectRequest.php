<?php

namespace App\Http\Requests;

use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'primary_language' => ['nullable', 'string', 'max:100'],
            'framework' => ['nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'string', Rule::enum(ProjectStatus::class)],
            'start_date' => ['nullable', 'date'],
            'estimated_end_date' => ['nullable', 'date'],
            'git_repository' => ['nullable', 'url', 'max:500'],
            'documentation_url' => ['nullable', 'url', 'max:500'],
        ];
    }
}
