<?php
namespace Byancode\Deploy\Providers;

use Byancode\Deploy\Commands\RunCommand;
use Illuminate\Support\ServiceProvider;

class DeployProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/deploy.php' => config_path('deploy.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                RunCommand::class,
            ]);
        }
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        # code ...
    }
}