<?php


namespace MwakalingaJohn\LaravelAutoMigrations\Migration\Actions;

class SchemaDrop extends SchemaAction{

    /**
     * The action to check for the current schema
     */
    private $action = "dropIfExists";

    public function process($schemaCall, $store)
    {
        if($schemaCall["method"] == $this->action){
            $tableName = $this->getTableFromSchemaCall($schemaCall);
            return $this->removeTable($store, $tableName);
        }
        return $store;
    }
}
