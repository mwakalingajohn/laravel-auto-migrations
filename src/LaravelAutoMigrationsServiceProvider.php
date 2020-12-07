<?php

namespace MwakalingaJohn\LaravelAutoMigrations;

use App\Console\Commands\MakeModel;
use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\ServiceProvider;
use MwakalingaJohn\LaravelAutoMigrations\Commands\MakeMigrationCommand;
use MwakalingaJohn\LaravelAutoMigrations\Commands\MakeModelCommand;

class LaravelAutoMigrationsServiceProvider extends ServiceProvider
{

    public function boot()
    {
    }

    public function register()
    {

        $this->app->bind(MakeMigrationCommand::class, function () {
            $creator = $this->app['migration.creator'];

            $composer = $this->app['composer'];

            return new MakeMigrationCommand($creator, $composer);
        });

        $this->commands([
            MakeModelCommand::class,
            MakeMigrationCommand::class
        ]);

        $this->app->bind("modelColumn", function () {
            return new ModelColumnManager;
        });
    }
}
