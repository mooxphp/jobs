<?php

namespace Adrolli\FilamentJobManager\Traits;

use Adrolli\FilamentJobManager\Models\Job;

trait QueueProgress
{
    /**
     * Update progress.
     */
    public function setProgress(int $progress): void
    {
        $progress = min(100, max(0, $progress));

        if (! $monitor = $this->getQueueMonitor()) {
            return;
        }

        $monitor->update([
            'progress' => $progress,
        ]);

        $this->progressLastUpdated = time();
    }

    /**
     * Return Queue Monitor Model.
     */
    protected function getQueueMonitor(): ?Job
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
