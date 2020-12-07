<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Commands;


use Illuminate\Database\Console\Migrations\MigrateMakeCommand as Command;

class MakeMigrationCommand extends Command
{

    public function __construct($creator, $composer)
    {
        parent::__construct($creator, $composer);
    }
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:auto-migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new list of migrations from the changes detected';
}
