<?php

namespace App\Observers;

use App\Jobs\AnalyzeProjectJob;
use App\Models\Project;

class ProjectObserver
{
    public function created(Project $project): void
    {
        AnalyzeProjectJob::dispatch($project);
    }

    public function updated(Project $project): void
    {
        AnalyzeProjectJob::dispatch($project);
    }
}
