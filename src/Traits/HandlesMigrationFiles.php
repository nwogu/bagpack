<?php

namespace Nwogu\Bagpack\Traits;

trait HandlesMigrationFiles
{
    /**
     * Get the migration path pattern that applies
     * 
     * @return string
     */
    protected function getMigrationPathPattern()
    {
        return with($this->defaultMigrationPath(), function ($path) {
            return $path . "/*";
        });
    }

    /**
     * Get the Migration file directories
     * 
     * @return array
     */
    protected function getMigrationFilesRecursively()
    {
        return glob( $this->getMigrationPathPattern(), GLOB_ONLYDIR);
    }

    /**
     * Get the default database migration path
     * @return string
     */
    protected function defaultMigrationPath()
    {
        return config('bagpack.path') ?: database_path('migrations');
    }

}