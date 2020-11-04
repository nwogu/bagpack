<?php

namespace Nwogu\Bagpack;

use Illuminate\Database\MigrationServiceProvider;
use Nwogu\Bagpack\Console\MigrateMakeCommand;

class MakeMigrationProvider extends MigrationServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerMigrateMakeCommand();
    }

     /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMigrateMakeCommand()
    {
        $this->app->singleton('command.migrate.make', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.creator'];

            $composer = $app['composer'];

            $files = $app['files'];

            return new MigrateMakeCommand($creator, $composer, $files);
        });
    }

}