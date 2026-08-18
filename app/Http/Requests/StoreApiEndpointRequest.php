<?php

namespace App\Http\Requests;

use App\Enums\EndpointStatus;
use App\Enums\HttpMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApiEndpointRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'environment_id' => ['required', 'exists:environments,id'],
            'name' => ['required', 'string', 'max:255'],
            'method' => ['required', 'string', Rule::enum(HttpMethod::class)],
            'url' => ['required', 'string', 'max:500'],
            'status' => ['sometimes', 'string', Rule::enum(EndpointStatus::class)],
        ];
    }
}
