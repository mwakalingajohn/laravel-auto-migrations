<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Migration;

use MwakalingaJohn\LaravelAutoMigrations\Migration\Actions\Action;
use MwakalingaJohn\LaravelAutoMigrations\Migration\Actions\ColumnAdd;
use MwakalingaJohn\LaravelAutoMigrations\Migration\Actions\ColumnChange;
use MwakalingaJohn\LaravelAutoMigrations\Migration\Actions\ColumnRemove;
use MwakalingaJohn\LaravelAutoMigrations\Migration\Actions\ColumnRename;
use MwakalingaJohn\LaravelAutoMigrations\Migration\Actions\SchemaCreate;
use MwakalingaJohn\LaravelAutoMigrations\Migration\Actions\SchemaDrop;

/**
 * TODO
 * - implement database change action
 */
class DatabaseMapper
{

    /**
     * The database state with tables and respective columns
     */
    private $databaseMap;

    /**
     * Schema actions to process
     */
    private const SCHEMA_ACTIONS = [
        SchemaDrop::class,
        SchemaCreate::class
    ];

    /**
     * Column actions to process
     */
    private const COLUMN_ACTIONS = [
        ColumnRemove::class,
        ColumnChange::class,
        ColumnRename::class,
        ColumnAdd::class
    ];

    public function mapFromMigrationStore($migrationStore)
    {
        $this->databaseMap = [];

        foreach ($migrationStore as $migration) {
            $schemaCalls = $this->getMigrationUpMethod($migration);
            foreach ($schemaCalls as $schemaCall) {
                foreach (self::SCHEMA_ACTIONS as $action) {
                    if($this->processAction(new $action, $schemaCall))
                        break;
                }
            }
        }
        return $this->databaseMap;
    }

    /**
     * Process action
     */
    public function processAction(Action $action, $schemaCall)
    {
        $this->databaseMap = $action->process($schemaCall, $this->databaseMap);
        if($action instanceof SchemaDrop && $action->didProcess())
            return $action->didProcess();

        $table = $this->getTableFromSchemaCall($schemaCall);

        foreach($this->getSchemaCallStatements($schemaCall) as $statement){
            foreach (self::COLUMN_ACTIONS as $columnAction) {
                if ($this->processColumnAction(new $columnAction($table), $statement))
                    break;
            }
        }
        return $action->didProcess();
    }

    /**
     * Process column actions
     */
    public function processColumnAction(Action $action, $statement)
    {
        $this->databaseMap = $action->process($statement, $this->databaseMap);
        return $action->didProcess();
    }

    /**
     * Get schema call statements
     */
    private function getSchemaCallStatements($schemaCall)
    {
        try {
            return $schemaCall["statements"];
        } catch (\Throwable $th) {
            dd("ERR: DatabaseMapper Ln 95:", "Failed, something wrong with your migrations!");
        }
    }


    /**
     * Get table name from schema call
     */
    private function getTableFromSchemaCall($schemaCall)
    {
        try {
            return $schemaCall["arg"];
        } catch (\Throwable $th) {
            dd("ERR: DatabaseMapper Ln 108:", "Failed, something wrong with your migrations!");
        }
    }

    /**
     * Get migration up method
     */
    private function getMigrationUpMethod($migration)
    {
        try {
            return $migration["tree"]["up"];
        } catch (\Throwable $th) {
            dd("ERR: DatabaseMapper Ln 120:", "Failed, something wrong with your migrations!");
        }
    }
}
