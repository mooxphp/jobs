# Filament Job Manager

Work in progress. Should become a Filament panel for managing job queues including failed jobs and batches. Currently buggy as hell.

## Installation

You should install the package via Composer:

```bash
composer require adrolli/filament-job-manager
php artisan vendor:publish --tag=filament-job-manager
```

### Authorization

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
