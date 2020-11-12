<?php

namespace Nwogu\Bagpack;

use Throwable;
use ReflectionMethod;
use Illuminate\Database\Migrations\Migrator;

class MigrationTranspiler
{
    /**
     * @var string $filePath
     */
    protected $filePath;

    /**
     * @var Illuminate\Database\Migrations\Migrator $migrator
     */
    protected $migrator;

    /**
     * @var Illuminate\Database\Migrations\Migration $migration
     */
    protected $migration;

    /**
     * @param Illuminate\Database\Migrations\Migrator
     * 
     */
    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;
    }

    /**
     * Set the migration file path
     * 
     * @param string $file
     * @return Nwogu\Bagpack\MigrationTranspiler
     */
    public function for($file)
    {
        $this->migrator->requireFiles([$file]);
        $name = key($this->migrator->getMigrationFiles($file));

        try {
            $this->migration = $this->migrator->resolve( $this->migrator->getMigrationName($name) );
        } catch (Throwable $exception) {
            $this->migration = null;
        }

        return $this;
    }

    /**
     * Get the Body of the migration method
     * 
     * @param string $method
     * @return string
     */
    public function getBody($method)
    {
        $function = new ReflectionMethod($this->migration, $method);

        $filePath = $function->getFileName();

        $start_line = $function->getStartLine() - 1;

        $end_line = $function->getEndLine();

        $length = $end_line - $start_line;

        $source = file_get_contents($filePath);

        $source = preg_split('/' . PHP_EOL . '/', $source);

        return implode(PHP_EOL, array_slice($source, $start_line, $length));
    }

    /**
     * Tries to guess the table name from the up/down
     * Migration method body.
     * 
     * @return string|null
     */
    public function findTableName()
    {
        if ($this->migration === null) return null;

        preg_match("/(?<=Schema::create\(').*?(?=',)/", $this->getBody("up"), $create);
        preg_match("/(?<=Schema::table\(').*?(?=',)/", $this->getBody("up"), $table);
        
        return collect($create)->first() ?: collect($table)->first();
    }
}