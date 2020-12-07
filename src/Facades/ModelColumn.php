<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Facades;

use Illuminate\Support\Facades\Facade;

class ModelColumn extends Facade{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'modelColumn';
    }
}
