<?php

namespace Nwogu\Bagpack\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Nwogu\Bagpack\MigrationTranspiler;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Contracts\Filesystem\Filesystem;

class MigrationPackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration:package {--p|path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Organize migration files into folders by their tables';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(MigrationTranspiler $transpiler)
    {
        if ( config('bagpack.run') == false ) {
            
            $this->alert("Bagpack disabled! Enable Bagpack to package migration files!");

            return 0;
        }

        $this->getMigrationFiles()->map(function ($path, $name) use ($transpiler) {

            $targetPath = $this->getTableMigrationPath($name, $path, $transpiler);

            $this->info("Moving $name from $path to $targetPath");

            $this->laravel->files->ensureDirectoryExists(dirname($targetPath));

            $this->laravel->files->move($path, $targetPath);
        });
    }

    /**
     * Get all the migration files paths
     * 
     * @return Illuminate\Support\Collection[string]
     */
    protected function getMigrationFiles()
    {
        $path = $this->option('path') ?: database_path('migrations');

        return collect($this->laravel->migrator->getMigrationFiles($path));
    }

    /**
     * Resolve name of migration file
     * 
     * @param string $file
     * @return string
     */
    protected function resolveName($file)
    {
        return Str::snake(implode('_', array_slice(explode('_', $file), 4)));
    }

    /**
     * Get the table migration path
     * 
     * @param string $name
     * @param string $path
     * @param Nwogu\Bagpack\MigrationTranspiler $transpiler
     * @return string
     */
    protected function getTableMigrationPath($name, $path, $transpiler)
    {

        $table = $transpiler->for($path)->findTableName();

        $parent = config('bagpack.path') ?: database_path('migrations');

        return "$parent/$table/$name.php";
    }
}
