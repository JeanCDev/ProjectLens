<?php

namespace App\Http\Requests;

use App\Enums\ReleaseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReleaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'version' => ['sometimes', 'string', 'max:50'],
            'changelog' => ['nullable', 'string'],
            'released_at' => ['nullable', 'date'],
            'status' => ['sometimes', 'string', Rule::enum(ReleaseStatus::class)],
        ];
    }
}
