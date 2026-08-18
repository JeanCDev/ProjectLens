<?php

namespace App\Models;

use App\Enums\EnvironmentStatus;
use Database\Factories\EnvironmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Environment extends Model
{
    /** @use HasFactory<EnvironmentFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'url',
        'database',
        'version',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => EnvironmentStatus::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function endpoints(): HasMany
    {
        return $this->hasMany(ApiEndpoint::class);
    }
}
