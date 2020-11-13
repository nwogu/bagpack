<?php

namespace Nwogu\Bagpack\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Nwogu\Bagpack\MigrationTranspiler;
use Illuminate\Database\Migrations\Migrator;
use Nwogu\Bagpack\Traits\InteractsWithFiles;
use Illuminate\Contracts\Filesystem\Filesystem;
use Nwogu\Bagpack\Traits\HandlesMigrationFiles;

class MigrationPackageCommand extends Command
{
    use InteractsWithFiles, HandlesMigrationFiles;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration:package {--p|path=} {--r|rollback}';

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
        if ($this->option('rollback')) {
            return $this->rollback();
        }

        return $this->package($transpiler);
    }

    /**
     * Package migrations
     * 
     * @return void
     */
    protected function package($transpiler)
    {
        $this->getMigrationFiles($this->migrationsPath())->map(function ($path, $name) use ($transpiler) {

            $targetPath = $this->getTableMigrationPath($name, $path, $transpiler);

            $this->info("Moving $name from $path to $targetPath");

            $this->ensureDirectoryExists($this->laravel->files, dirname($targetPath));

            $this->laravel->files->move($path, $targetPath);
        });
    }

    /**
     * Rollback packaged migrations
     * 
     * @return void
     */
    protected function rollback()
    {
        $migrationDirectories   = $this->getMigrationFilesRecursively();
        $migrationsPath         = $this->migrationsPath();

        $this->ensureDirectoryExists($this->laravel->files, $migrationsPath);

        $this->getMigrationFiles($migrationDirectories)->map(function ($path, $name) use ($migrationsPath) {

            $targetPath = "$migrationsPath/$name.php";

            $this->info("Moving $name from $path to $targetPath");

            $this->laravel->files->move($path, $targetPath);
        });

        $this->laravel->files->deleteDirectories($migrationsPath);
    }

    /**
     * Get all the migration files paths
     * 
     * @return Illuminate\Support\Collection[string]
     */
    protected function getMigrationFiles($path)
    {
        return collect($this->laravel->migrator->getMigrationFiles($path));
    }

    /**
     * Get Migrations Path
     * 
     * @return string
     */
    protected function migrationsPath()
    {
        return $this->option('path') ?: database_path('migrations');
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

        $parent = $this->defaultMigrationPath();

        return "$parent/$table/$name.php";
    }
}
