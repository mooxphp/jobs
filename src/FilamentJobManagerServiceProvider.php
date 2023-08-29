<?php

namespace Adrolli\FilamentJobManager;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentJobManagerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-job-manager');

        $this->publishes([
            __DIR__.'/../config/filament-job-manager.php' => config_path('filament-job-manager.php'),
        ], 'filament-job-manager');

    }
}
