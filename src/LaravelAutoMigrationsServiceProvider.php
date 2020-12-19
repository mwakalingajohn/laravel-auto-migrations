<?php

namespace MwakalingaJohn\LaravelAutoMigrations;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use MwakalingaJohn\LaravelAutoMigrations\Commands\{MakeMigrationCommand, MakeModelCommand};
use MwakalingaJohn\LaravelAutoMigrations\Manager\ModelColumnManager;
use MwakalingaJohn\LaravelAutoMigrations\Migration\{Handler, Parser, Writer, Reader as MigrationReader, ChangeDetector};
use MwakalingaJohn\LaravelAutoMigrations\Model\Reader as ModelReader;

class LaravelAutoMigrationsServiceProvider extends ServiceProvider
{

    public function boot()
    {
    }

    public function register()
    {
        $this->registerCommands();

        $this->registerBindings();

        $this->registerFacades();
    }

    /**
     * Register package commands
     */
    public function registerCommands()
    {
        $this->commands([
            MakeModelCommand::class,
            MakeMigrationCommand::class
        ]);
    }

    /**
     * Create class bindings for the package
     */
    public function registerBindings()
    {
        $this->app->bind(MakeMigrationCommand::class, function () {
            $creator = $this->app['migration.creator'];
            $composer = $this->app['composer'];
            return new MakeMigrationCommand($creator, $composer);
        });
        $this->app->bind(Handler::class, function () {
            return new Handler(
                new MigrationReader(new Filesystem, new Parser),
                new ModelReader,
                new Writer,
                new ChangeDetector
            );
        });
    }

    /**
     * Register classes to the service container to be used
     * as facades
     */
    public function registerFacades()
    {
        $this->app->bind("modelColumn", function () {
            return new ModelColumnManager;
        });
    }
}
