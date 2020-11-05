<?php

namespace Nwogu\Bagpack;

use Illuminate\Support\ServiceProvider;
use Nwogu\Bagpack\Console\MigrationPackageCommand;

class BagpackServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MigrationTranspiler::class, function ($app) {
            return new MigrationTranspiler($app['migrator']);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrationPackageCommand::class
            ]);
        }

        foreach (glob( $this->getMigrationPathPattern(), GLOB_ONLYDIR) as $migrationDir) {
            $this->app['migrator']->path($migrationDir);
        }

        $this->publishes([
            __DIR__ . "/../config/bagpack.php"  => config_path('bagpack.php')
        ], 'bagpack');
    }

    /**
     * Get the migration path pattern that applies
     * 
     * @return string
     */
    protected function getMigrationPathPattern()
    {
        return with(config('bagpack.path') ?? database_path('migrations'), function ($path) {
            return $path . "/*";
        });
    }
}