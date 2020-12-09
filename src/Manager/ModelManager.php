<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Manager;

use Illuminate\Database\Eloquent\Model;

class ModelManager extends Model
{


    /**
     *   Include timestamps properties
     *   @param $model
     */
    protected $hasTimeStamps = true;

    /**
     *  Declare columns function
     */
    protected function columns(){}

    /**
     * Get the columns
     */
    public function getColumns()
    {
        return $this->columns();
    }
}
