<?php

namespace Stery\Api\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use LaravelJsonApi\Laravel\Console\MakeServer;
use Symfony\Component\Console\Input\InputArgument;

class MakeApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:api {name}';

    private $config;

    private $files;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate JSON:API Scaffold of a resource';


    public function __construct()
    {
        parent::__construct();
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of the resource');       

        $this->config = Config::get('stery-api', require(__DIR__ . '/../../resources/config.php')); 

        $this->files = new Filesystem;
    }

    public function inputName()
    {
        return $this->argument('name');
    }

    public function checkStubs()
    {
        $jsonApiStubDir = $this->laravel->basePath('stubs/jsonapi');

        if(!is_dir($jsonApiStubDir)) {
            mkdir($jsonApiStubDir, 0777, true);
        }
        
        $stubs = scandir(__DIR__ . '/../../stubs/jsonapi');

        array_splice($stubs, array_search(".", $stubs), 1);
        array_splice($stubs, array_search("..", $stubs), 1);

        $this->line('Checking Stubs...');
        $bar = $this->output->createProgressBar(sizeof($stubs));

        foreach($stubs as $stub) {
            $source = sprintf("%s/%s", __DIR__ . "/../../stubs/jsonapi", $stub);
            $output = sprintf("%s/%s", $jsonApiStubDir, $stub);

            if(!is_file($output)) {
                $this->files->copy($source, $output);
            }
            $bar->advance();
        }

        $bar->finish();

        $this->newLine();
    }
    
    public function handle()
    {
        $this->checkStubs();
        $this->makeServer();
        $this->makeController();
        $this->makeModel();
        $this->makeSchema();
        $this->makeAuthorizer();
        $this->makeRequests();
        $this->makeResource();
    }

    public function makeResource()
    {
        $resourceClass = sprintf(
            "App\\%s\\%s\\%s\\%sResource",
            config('jsonapi.namespace'),
            Str::studly($this->config['server']['name']),
            Str::studly(Str::plural($this->inputName())),
            Str::studly(Str::singular($this->inputName())),
        );

        if(!class_exists($resourceClass)) {
            $this->runCommand('jsonapi:resource', [
                'name' => $this->inputName(),
                '--server' => $this->getServer(),
            ], $this->output);

            return;
        }
        
        $this->outputExists('Skipping Resource');
    }

    public function makeRequests()
    {
        $requestClass = sprintf(
            "App\\%s\\%s\\%s\\%sRequest",
            config('jsonapi.namespace'),
            Str::studly($this->config['server']['name']),
            Str::studly(Str::plural($this->inputName())),
            Str::studly(Str::singular($this->inputName())),
        );
        $queryClass = sprintf(
            "App\\%s\\%s\\%s\\%sQuery",
            config('jsonapi.namespace'),
            Str::studly($this->config['server']['name']),
            Str::studly(Str::plural($this->inputName())),
            Str::studly(Str::singular($this->inputName())),
        );

        $collectionQueryClass = sprintf(
            "App\\%s\\%s\\%s\\%sCollectionQuery",
            config('jsonapi.namespace'),
            Str::studly($this->config['server']['name']),
            Str::studly(Str::plural($this->inputName())),
            Str::studly(Str::singular($this->inputName())),
        );

        if(
            !class_exists($requestClass) ||
            !class_exists($queryClass) ||
            !class_exists($collectionQueryClass)
        ) {
            $this->runCommand('jsonapi:requests', [
                'name' => $this->inputName(),
                '--server' => $this->getServer(),
            ], $this->output);

            return;

        }

        $this->outputExists('Skipping Requests');

    }

    public function makeSchema()
    {
        $className = sprintf(
            "App\\%s\\%s\\%s\\%sSchema",
            config('jsonapi.namespace'),
            Str::studly($this->config['server']['name']),
            Str::studly(Str::plural($this->inputName())),
            Str::studly(Str::singular($this->inputName())),
        );

        if(!class_exists($className)) {
            $this->runCommand('make:schema', [
                'name' => $this->inputName(),
                '--server' => $this->getServer(),
                '--primary' => $this->config['models']['primary_type'],
            ], $this->output);

            return;
        }

        $this->outputExists('Skipping Schema');
    }

    public function makeAuthorizer()
    {
        $className = sprintf(
            "App\\%s\\%s\\%s\\%sAuthorizer",
            config('jsonapi.namespace'),
            Str::studly($this->config['server']['name']),
            Str::studly(Str::plural($this->inputName())),
            Str::studly(Str::singular($this->inputName())),
        );

        if(!class_exists($className)) {
            $this->runCommand('jsonapi:authorizer', [
                'name' => $this->inputName(),
                '--server' => $this->getServer(),
                '--resource' => true
            ], $this->output);
            return;
        }

        $this->outputExists('Skipping Authorizer');

    }

    public function getServer()
    {
        return $this->config['server']['name'];
    }

    public function makeServer()
    {
        $serverClassName = sprintf(
            "App\\%s\\%s\\Server",
            config('jsonapi.namespace'),
            $this->config['server']['name']);

        if(!class_exists($serverClassName))
        {
            $this->runCommand('jsonapi:server', [
                'name' => $this->config['server']['name'],
                '--uri' => Str::snake($this->config['server']['name']),
            ], $this->output);

            return;
        }

        $this->outputExists('Skipping Server');
    }

    public function makeModel()
    {

        $modelName = Str::studly(Str::singular($this->inputName()));

        $modelClassName = sprintf("%s\\%s", $this->config['models']['namespace'], $modelName);

        if(!class_exists($modelClassName)) {
            $this->runCommand('jsonapi:model', [
                'name' => $modelClassName,
                '--factory' => 'true',
                '--migration' => !class_exists($modelClassName),
                '--primary' => $this->config['models']['primary_type'],
            ], $this->output);

            return;
        }
        
        $this->outputExists('Skipping Model');
    }

    public function makeController()
    {
        $controllerName = sprintf(
            "%s\\%sController",
            $this->config['controller']['namespace'],
            Str::studly(Str::singular($this->inputName()))
        );

        if(!class_exists($controllerName)) {
            $this->runCommand('jsonapi:controller', [
                'name' => $controllerName,
            ], $this->output);

            return;
        }

        $this->outputExists('Skipping Controller');
    }

    public function outputExists(string $str)
    {
        $line = $str . ' ';
        $len = 50 - strlen($str);
        for ($i=0; $i < $len; $i++) { 
            $line .= '.';
        }

        $line .= ' Already exists';

        $this->line($line);
    }

}