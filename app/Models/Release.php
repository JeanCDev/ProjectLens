<?php

namespace App\Models;

use App\Enums\ReleaseStatus;
use Database\Factories\ReleaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Release extends Model
{
    /** @use HasFactory<ReleaseFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'version',
        'changelog',
        'released_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReleaseStatus::class,
            'released_at' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
