<?php

namespace Nwogu\Bagpack\Console;

use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand as BaseCommand;

class MigrateMakeCommand extends BaseCommand
{
    /**
     * The Filesystem
     *
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new migration install command instance.
     *
     * @param  \Illuminate\Database\Migrations\MigrationCreator  $creator
     * @param  \Illuminate\Support\Composer  $composer
     * @param   Illuminate\Filesystem\Filesystem $files
     * @return void
     */
    public function __construct(MigrationCreator $creator, Composer $composer, Filesystem $files)
    {
        parent::__construct($creator, $composer);

        $this->files = $files;
    }

    /**
     * Write the migration file to disk.
     *
     * @param  string  $name
     * @param  string  $table
     * @param  bool  $create
     * @return string
     */
    protected function writeMigration($name, $table, $create)
    {
        $targetPath = $this->getMigrationPath();

        if ($this->shouldPackageMigrationFiles()) {

            $targetPath = "{$targetPath}/{$table}";

            $this->files->exists($targetPath) ?: $this->files->makeDirectory($targetPath);
        }

        $file = $this->creator->create(
            $name, $targetPath, $table, $create
        );

        if (! $this->option('fullpath')) {
            $file = pathinfo($file, PATHINFO_FILENAME);
        }

        $this->line("<info>Created Migration:</info> {$file}");
    }

    /**
     * Get migrations path from config
     * 
     * @return string
     */
    protected function targetMigrationPath()
    {
        return config('bagpack.path', $this->getMigrationPath());
    }

    /**
     * Determine if bagpack should package migration file
     * 
     * @return bool
     */
    protected function shouldPackageMigrationFiles()
    {
        return config('bagpack.run', true);
    }
    
}