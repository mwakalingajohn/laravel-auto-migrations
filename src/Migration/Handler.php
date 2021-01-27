<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Migration;

use MwakalingaJohn\LaravelAutoMigrations\Model\Reader as ModelReader;
use MwakalingaJohn\LaravelAutoMigrations\Migration\Reader as MigrationReader;

class Handler
{

    /**
     * Store the migration state
     */
    private $migrationStore;

    /**
     * Store the model state
     */
    private $modelStore;

    /**
     * Migration Reader
     */
    private MigrationReader $migrationReader;

    /**
     * Model Reader
     */
    private ModelReader $modelReader;

    /**
     * Writer
     */
    private Writer $writer;

    /**
     * Change detector
     */
    private ChangeDetector $changeDector;

    public function __construct(
        MigrationReader $migrationReader,
        ModelReader $modelReader,
        Writer $writer,
        ChangeDetector $changeDector
    ) {

        $this->migrationReader = $migrationReader;
        $this->modelReader = $modelReader;
        $this->changeDector = $changeDector;
        $this->writer = $writer;
    }

    /**
     * Read migrations
     */
    public function readMigrations()
    {
        $this->migrationStore = $this->migrationReader->get();
    }

    /**
     * Read auto models
     */
    public function readModels()
    {
        $this->modelStore = $this->modelReader->get();
    }

    /**
     * Write migrations
     */
    public function write()
    {
    }

    /**
     * Detect changes
     */
    public function detectChanges()
    {
    }

    /**
     * Get the model store
     */
    public function getModelStore()
    {
        return $this->modelStore;
    }
}
