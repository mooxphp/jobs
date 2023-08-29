<?php

namespace Adrolli\FilamentJobManager\Traits;

use Adrolli\FilamentJobManager\Models\Job;

trait JobProgress
{
    /**
     * Update progress.
     */
    public function setProgress(int $progress): void
    {
        $progress = min(100, max(0, $progress));

        if (! $monitor = $this->getJobMonitor()) {
            return;
        }

        $monitor->update([
            'progress' => $progress,
        ]);

        $this->progressLastUpdated = time();
    }

    /**
     * Return Job Monitor Model.
     */
    protected function getJobMonitor(): ?Job
    {
        if (! property_exists($this, 'job')) {
            return null;
        }

        if (! $this->job) {
            return null;
        }

        if (! $jobId = Job::getJobId($this->job)) {
            return null;
        }

        $model = Job::getModel();

        return $model::whereJobId($jobId)
            ->orderBy('started_at', 'desc')
            ->first();
    }
}
