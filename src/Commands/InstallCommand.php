<?php

namespace Moox\Jobs\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mooxjobs:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish and migrate Moox Jobs Package';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->comment('Publishing Jobs Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'jobs-config']);

        $this->comment('Publishing Jobs Migrations...');
        $this->callSilent('vendor:publish', ['--tag' => 'jobs-migrations']);

        if (config('queue.default') === 'database') {
            $this->comment('Creating Queue Tables...');
            $this->callSilent('queue:table');
            $this->callSilent('queue:failed-table');
            $this->callSilent('queue:batches-table');
        }

        $this->comment('Running Migrations...');
        $this->call('migrate');

        $this->comment('Registering the Filament Resources...');

        $providerPath = app_path('Providers/Filament/AdminPanelProvider.php');

        if (File::exists($providerPath)) {

            $content = File::get($providerPath);

            $pluginsToAdd = [
                'JobsPlugin::make(),',
                'JobsWaitingPlugin::make(),',
                'JobsFailedPlugin::make(),',
                'JobsBatchesPlugin::make(),',
            ];

            $pattern = '/->plugins\(\[([\s\S]*?)\]\);/';

            $replacement = function ($matches) use ($pluginsToAdd) {
                $existingPlugins = trim($matches[1]);
                $newPlugins = implode("\n", $pluginsToAdd);

                return "->plugins([\n".$existingPlugins."\n".$newPlugins."\n]);";
            };

            $newContent = preg_replace_callback($pattern, $replacement, $content);

            File::put($providerPath, $newContent);

            $this->info('Plugins added to AdminPanelProvider.');

        } else {

            $this->error('AdminPanelProvider not found.');

        }

        $this->info('Moox Jobs was installed successfully');
    }
}