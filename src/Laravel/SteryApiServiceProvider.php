<?php

namespace Stery\Api\Laravel;

use Illuminate\Support\ServiceProvider;
use LaravelJsonApi\Laravel\Console as JsonApi;
use LaravelJsonApi\Laravel\ServiceProvider as BaseProvider;
use Stery\Api\Laravel\Commands\MakeApiCommand;
use Stery\Api\Laravel\Commands\MakeModelCommand;
use Stery\Api\Laravel\Commands\MakeSchemaCommand;

class SteryApiServiceProvider extends BaseProvider
{
    public function register(): void
    {
        parent::register();
        $this->publishes([

        ]);

        $this->commands([
            MakeApiCommand::class,
            MakeModelCommand::class,
            MakeSchemaCommand::class,
            // JsonApi\MakeController::class
        ]);
    }

    public function resourcePath(string $res)
    {
        return __DIR__ . '/resources/' . $res;
    }
}