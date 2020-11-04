<?php

namespace Nwogu\Bagpack;

use Illuminate\Support\ServiceProvider;

class BagpackServiceProvidern extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach (glob( $this->getMigrationPathPattern(), GLOB_ONLYDIR) as $migrationDir) {
            $this->app['migrator']->path($migrationDir);
        }
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