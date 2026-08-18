<?php

namespace App\Models;

use App\Enums\EndpointStatus;
use App\Enums\HttpMethod;
use Database\Factories\ApiEndpointFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiEndpoint extends Model
{
    /** @use HasFactory<ApiEndpointFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'environment_id',
        'name',
        'method',
        'url',
        'status',
        'last_checked_at',
        'response_time_ms',
    ];

    protected function casts(): array
    {
        return [
            'method' => HttpMethod::class,
            'status' => EndpointStatus::class,
            'last_checked_at' => 'datetime',
            'response_time_ms' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function environment(): BelongsTo
    {
        return $this->belongsTo(Environment::class);
    }
}
