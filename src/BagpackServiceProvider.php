<?php

namespace Nwogu\Bagpack;

use Nwogu\Bagpack\Extensions\Migrator;
use Illuminate\Support\ServiceProvider;
use Nwogu\Bagpack\Traits\HandlesMigrationFiles;
use Nwogu\Bagpack\Console\MigrationPackageCommand;

class BagpackServiceProvider extends ServiceProvider
{
    use HandlesMigrationFiles;
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // The migrator is responsible for actually running and rollback the migration
        // files in the application. We'll pass in our database connection resolver
        // so the migrator can resolve any of these connections when it needs to.
        $this->app->extend('migrator', function ($migrator, $app) {
            $repository = $app['migration.repository'];

            return new Migrator($repository, $app['db'], $app['files'], $app['events']);
        });

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

        foreach ($this->getMigrationFilesRecursively() as $migrationDir) {
            $this->app['migrator']->path($migrationDir);
        }

        $this->publishes([
            __DIR__ . "/../config/bagpack.php"  => config_path('bagpack.php')
        ], 'bagpack');
    }
}