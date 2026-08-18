<?php

namespace App\Http\Requests;

use App\Enums\EnvironmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEnvironmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:500'],
            'database' => ['nullable', 'string', 'max:255'],
            'version' => ['nullable', 'string', 'max:50'],
            'status' => ['sometimes', 'string', Rule::enum(EnvironmentStatus::class)],
        ];
    }
}
