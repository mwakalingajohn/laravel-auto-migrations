<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Model;

use MwakalingaJohn\LaravelAutoMigrations\BaseReader;
use MwakalingaJohn\LaravelAutoMigrations\Manager\ModelManager;

/**
 * TODO
 * 1. Include table in the array
 */
class Reader extends BaseReader
{
    /**
     * Get the model store from auto migrations
     */
    public function get()
    {
        $directory = $this->getModelsDirectory();

        $files = $this->fileNames($directory);

        return collect($files)
            ->map(function ($file) {
                return $this->resolve($this->getName($file));
            })
            ->filter(function ($instance) {
                return $this->isAnAutoModel($instance);
            })->map(function ($instance) {
                return $this->getColumns($instance);
            });
    }

    /**
     * Check if model is an auto model
     */
    public function isAnAutoModel($instance)
    {
        return method_exists($instance, 'getColumns');
    }

    /**
     * Get columns from the model
     */
    public function getColumns(ModelManager $instance)
    {
        return $instance->getColumns();
    }

    /**
     * Get auto models directory
     */
    public function getModelsDirectory()
    {
        return app_path('Models');
    }

    /**
     * Models namespace
     */
    protected function getNamespace()
    {
        return "App\\Models\\";
    }
}
