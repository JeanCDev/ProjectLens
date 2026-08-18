<?php

namespace App\Http\Requests;

use App\Enums\EndpointStatus;
use App\Enums\HttpMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApiEndpointRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'environment_id' => ['sometimes', 'exists:environments,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'method' => ['sometimes', 'string', Rule::enum(HttpMethod::class)],
            'url' => ['sometimes', 'string', 'max:500'],
            'status' => ['sometimes', 'string', Rule::enum(EndpointStatus::class)],
        ];
    }
}
