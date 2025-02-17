<?php

namespace Moox\Jobs\Traits;

use Moox\Jobs\Models\JobManager;

trait JobProgress
{
    public $progressLastUpdated;

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
    protected function getJobMonitor(): ?JobManager
    {
        /** @phpstan-ignore-next-line */
        if (! property_exists($this, 'job')) {
            return null;
        }

        if (! $this->job) {
            return null;
        }

        if (! $jobId = JobManager::getJobId($this->job)) {
            return null;
        }

        $model = JobManager::getModel();

        /* @phpstan-ignore-next-line */
        return $model::whereJobId($jobId)
            ->orderBy('started_at', 'desc')
            ->first();
    }
}
