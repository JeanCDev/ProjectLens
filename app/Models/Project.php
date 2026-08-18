<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'primary_language',
        'framework',
        'status',
        'start_date',
        'estimated_end_date',
        'git_repository',
        'documentation_url',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'start_date' => 'date',
            'estimated_end_date' => 'date',
        ];
    }

    public function environments(): HasMany
    {
        return $this->hasMany(Environment::class);
    }

    public function releases(): HasMany
    {
        return $this->hasMany(Release::class);
    }

    public function endpoints(): HasMany
    {
        return $this->hasMany(ApiEndpoint::class);
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }
}
