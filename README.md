# Filament Job Manager

Work in progress. Should become a Filament panel for managing job queues including failed jobs and batches.

Contains some bad bugs.

## Installation

Install the package via Composer:

```bash
composer require adrolli/filament-job-manager
```

Create the necessary tables:

```bash
php artisan vendor:publish --tag="filament-job-manager-migrations"

# Queue tables, if using the database driver instead of Redis queue backend
php artisan queue:table
php artisan queue:failed-table
php artisan queue:batches-table

php artisan migrate
```

Publish the config file with:

```bash
php artisan vendor:publish --tag="filament-job-manager-config"
```

This is the content of the published config file:

```php
<?php

return [
    'resources' => [
        'jobs' => [
            'enabled' => true,
            'label' => 'Job',
            'plural_label' => 'Jobs',
            'navigation_group' => 'Job manager',
            'navigation_icon' => 'heroicon-o-cpu-chip',
            'navigation_sort' => 1,
            'navigation_count_badge' => true,
            'resource' => Adrolli\FilamentJobManager\Resources\JobsResource::class,
        ],
        'failed_jobs' => [
            'enabled' => true,
            'label' => 'Failed Job',
            'plural_label' => 'Failed Jobs',
            'navigation_group' => 'Job manager',
            'navigation_icon' => 'heroicon-o-exclamation-circle',
            'navigation_sort' => 2,
            'navigation_count_badge' => true,
            'resource' => Adrolli\FilamentJobManager\Resources\FailedJobsResource::class,
        ],
        'job_batches' => [
            'enabled' => true,
            'label' => 'Job Batch',
            'plural_label' => 'Job Batches',
            'navigation_group' => 'Job manager',
            'navigation_icon' => 'heroicon-o-inbox-stack',
            'navigation_sort' => 3,
            'navigation_count_badge' => true,
            'resource' => Adrolli\FilamentJobManager\Resources\JobBatchesResource::class,
        ],
    ],
    'pruning' => [
        'enabled' => true,
        'retention_days' => 7,
    ],
];

```

Register the Plugins in `app/Providers/Filament/AdminPanelProvider.php`:

```php
    ->plugins([
	FilamentJobManagerPlugin::make(),
	FilamentFailedJobsPlugin::make(),
	FilamentJobBatchesPlugin::make(),
    ])
```

You don't need to register all Resources. If you don't use Job Batches, you can hide this feature by not registering it.

Instead of publishing and modifying the config-file, you can also do all settings in AdminPanelProvider like so:

```php
    ->plugins([
	FilamentJobManagerPlugin::make()
	    ->label('Job runs')
	    ->pluralLabel('Jobs that seems to run')
	    ->enableNavigation(true)
	    ->navigationIcon('heroicon-o-face-smile')
	    ->navigationGroup('My Jobs and Queues')
	    ->navigationSort(5)
	    ->navigationCountBadge(true)
	    ->enablePruning(true)
	    ->pruningRetention(7),
	FilamentFailedJobsPlugin::make()
	    ->label('Job failed')
	    ->pluralLabel('Jobs that failed hard')
	    ->enableNavigation(true)
	    ->navigationIcon('heroicon-o-face-frown')
	    ->navigationGroup('My Jobs and Queues')
	    ->navigationSort(5)
	    ->navigationCountBadge(true)
    ])
```

## Usage

Just run a Background Job and go to the route `/admin/jobs` to see the jobs.

## Example Job

You do not need to change anything in your Jobs to work with Filament Job Monitor. But especially for long running jobs you may find this example interesting:

```php
<?php

namespace App\Jobs;

use Adrolli\FilamentJobManager\Traits\JobProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use
class JobMonitorDemo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, QueueProgress;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        $count = 0;
        $steps = 10;
        $final = 100;

        while ($count < $final) {
            $this->setProgress($count);
            $count = $count + $steps;
          	sleep(10);
        }
    }
}
```

## Authorization - outdated!

Outdated. Use Shield instead.

If you would like to prevent certain users from accessing your page, you should register a policy:

```php
use App\Policies\FailedJobPolicy;
use Adrolli\FilamentJobManager\Models\FailedJob;
use Adrolli\FilamentJobManager\Models\JobBatch;

class AuthServiceProvider extends ServiceProvider
{
	protected $policies = [
		FailedJob::class => FailedJobPolicy::class,
		JobBatch::class  => JobBatchPolicy::class,
	];
}
```

```php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FailedJobPolicy
{
	use HandlesAuthorization;

	public function viewAny(User $user): bool
	{
		return $user->can('manage_failed_jobs');
	}
}
```

(same for JobPolicy and JobBatchPolicy, if necessary).

This will prevent the navigation item(s) from being registered.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
