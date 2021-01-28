<?php


namespace MwakalingaJohn\LaravelAutoMigrations\Migration\Actions;

abstract class SchemaAction implements Action
{

    protected bool $didProcess = false;

    public abstract function process($branch, $store);

    public function removeTable($databaseMap, $tableName)
    {
        unset($databaseMap[$tableName]);
        return $databaseMap;
    }

    public function addTable($databaseMap, $tableName)
    {
        $databaseMap[$tableName] = [];
        return $databaseMap;
    }

    /**
     * Get table name from schema call
     */
    protected function getTableFromSchemaCall($schemaCall)
    {
        try {
            return $schemaCall["arg"];
        } catch (\Throwable $th) {
            dd("ERR: DatabaseMapper Ln 77:", "Failed, something wrong with your migrations!");
        }
    }

    /**
     * Check if it processed anything
     */
    public function didProcess(): bool
    {
        return $this->didProcess;
    }
}
