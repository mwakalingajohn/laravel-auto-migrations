<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Commands;

use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Support\Composer;
use MwakalingaJohn\LaravelAutoMigrations\Migration\Handler;
use PhpParser\Node\Stmt\TryCatch;

class MakeMigrationCommand extends MigrationBaseCommand
{
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

    /**
     * The migration creator instance.
     *
     * @var \Illuminate\Database\Migrations\MigrationCreator
     */
    protected $creator;

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a new migration install command instance.
     *
     * @param  \Illuminate\Database\Migrations\MigrationCreator  $creator
     * @param  \Illuminate\Support\Composer  $composer
     * @return void
     */
    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("Creating auto migrations");

        $handler = $this->getHandler();

        $handler->readModels();
        $handler->readMigrations();


        // $this->composer->dumpAutoloads();
    }

    /**
     * Get the main migration handler
     * - returns from the service container
     */
    public function getHandler() : Handler
    {
        return app()->make(Handler::class);
    }
}
