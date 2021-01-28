<?php


namespace MwakalingaJohn\LaravelAutoMigrations\Migration\Actions;

class SchemaCreate extends SchemaAction{

    /**
     * The action to check for the current schema
     */
    private $action = "create";

    public function process($schemaCall, $store)
    {
        if($schemaCall["method"] == $this->action){
            $tableName = $this->getTableFromSchemaCall($schemaCall);
            return $this->addTable($store, $tableName);
        }
        return $store;
    }

}
