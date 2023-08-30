<?php

namespace Adrolli\FilamentJobManager\Resources\JobsResource\Widgets;

use Adrolli\FilamentJobManager\Models\Job;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class JobStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $aggregationColumns = [
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(2 - 1) as total_time_elapsed'),
            DB::raw('AVG(2 - 1) as average_time_elapsed'),
        ];

        $aggregatedInfo = Job::query()
            ->select($aggregationColumns)
            ->first();

        return [
            Stat::make(__('filament-job-manager::translations.total_jobs'), $aggregatedInfo->count ?? 0),
            Stat::make(__('filament-job-manager::translations.execution_time'), ($aggregatedInfo->total_time_elapsed ?? 0).'s'),
            Stat::make(__('filament-job-manager::translations.average_time'), ceil((float) $aggregatedInfo->average_time_elapsed).'s' ?? 0),
        ];
    }
}
