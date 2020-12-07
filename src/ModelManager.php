<?php

namespace MwakalingaJohn\LaravelAutoMigrations;

use Illuminate\Database\Eloquent\Model;

class ModelManager extends Model{

    /**
    *   Column definitions for the database
    *   @param $properties
    */
    protected $properties = [];


    /**
    *   Include timestamps properties
    *   @param $model
    */
    protected $hasTimeStamps = true;
}
