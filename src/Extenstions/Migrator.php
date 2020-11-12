<?php

namespace Nwogu\Bagpack\Extensions;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Database\Migrations\Migrator as IlluminateMigrator;

/**
 * This extensions is to support lower laravel versions
 */
class Migrator extends IlluminateMigrator
{
    /**
     * Get all of the migration files in a given path.
     *
     * @param  string|array  $paths
     * @return array
     */
    public function getMigrationFiles($paths)
    {
        return Collection::make($paths)->flatMap(function ($path) {
            return Str::endsWith($path, '.php') ? [$path] : $this->files->glob($path.'/*_*.php');
        })->filter()->values()->keyBy(function ($file) {
            return $this->getMigrationName($file);
        })->sortBy(function ($file, $key) {
            return $key;
        })->all();
    }
}