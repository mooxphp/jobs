<?php

namespace Adrolli\FilamentJobManager\Resources\JobsWaitingResource\Pages;

use Adrolli\FilamentJobManager\Resources\JobsWaitingResource;
use Adrolli\FilamentJobManager\Resources\JobsWaitingResource\Widgets\JobsWaitingOverview;
use Filament\Resources\Pages\ListRecords;

class ListJobsWaiting extends ListRecords
{
    public static string $resource = JobsWaitingResource::class;

    public function getActions(): array
    {
        return [];
    }

    public function getHeaderWidgets(): array
    {
        return [
            JobsWaitingOverview::class,
        ];
    }

    public function getTitle(): string
    {
        return __('filament-job-manager::translations.title');
    }
}
