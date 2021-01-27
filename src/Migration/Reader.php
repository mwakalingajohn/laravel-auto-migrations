<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Migration;

use Illuminate\Filesystem\Filesystem;
use MwakalingaJohn\LaravelAutoMigrations\BaseReader;
use Illuminate\Support\Str;
use MwakalingaJohn\LaravelAutoMigrations\Migration\Parser;

class Reader extends BaseReader
{
    /**
     * Filesystem object
     */
    private Filesystem $files;

    /**
     * If should get auto migrations by default
     */
    private bool $readAutoMigrationsOnly = false;

    /**
     * Should use class instances or raw files
     */
    private bool $useClassInstances = false;

    /**
     * Migration parser
     */
    private Parser $parser;

    /**
     * Just learned laravel migrations have no namespace, hence need to
     * be required at runtime using the Filesystem
     */
    public function __construct(Filesystem $files, Parser $parser)
    {
        $this->files = $files;
        $this->parser = $parser;
    }

    /**
     * Get the migrations store
     */
    public function get()
    {
        $migrations = $this->getMigrations();
        return $this->parser->parse($migrations);
    }

    /**
     *  Get list of all migrations available
     *  - Checking if should have auto migrations or not
     * @return array of migration instances
     */
    public function getMigrations()
    {
        $directory = $this->getMigrationsDirectory();

        $files = $this->fileNames($directory); 
        return $this->useClassInstances ?
            $this->mapMigrationsToInstances($directory, $files) :
            $this->mapMigrationsToAbsolutePaths($directory, $files);
    }

    /**
     * Map migrations to absolute paths
     */
    public function mapMigrationsToAbsolutePaths($directory, $files)
    {
        return collect($files)
            ->map(function ($file) use ($directory) {
                return "$directory/$file";
            });
    }

    /**
     * Map migrations to instances
     */
    public function mapMigrationsToInstances($directory, $files)
    {
        // Require the migrations before resolving them
        $this->requireMigrations($directory, $files);

        return collect($files)
            ->map(function ($file) {
                return $this->resolve($this->className($file));
            })->filter(function ($instance) {
                return $this->readAutoMigrationsOnly ?
                    $this->isAutoMigration($instance) :
                    $instance;
            });
    }


    /**
     * Check if class is an auto migration
     */
    public function isAutoMigration($instance)
    {
        return property_exists($instance, "isAutoMigration");
    }

    /**
     *  Get class name from the complicated model name
     */
    public function className($file_name)
    {
        return Str::studly(str_replace(".php", "", implode("_", array_slice(explode('_', $file_name), 4))));
    }

    /**
     * Get the absolute path to the migrations directory
     */
    public function getMigrationsDirectory()
    {
        return database_path('migrations');
    }

    /**
     * Require the migrations
     */
    public function requireMigrations($directory, $migrations)
    {
        foreach ($migrations as $migration) {
            $this->files->requireOnce("$directory/$migration");
        }
    }

    /**
     * Migrations default namespace
     */
    protected function getNamespace()
    {
        return "";
    }
}
