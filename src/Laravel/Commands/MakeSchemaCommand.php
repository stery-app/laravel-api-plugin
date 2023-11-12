<?php

namespace Stery\Api\Laravel\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use LaravelJsonApi\Laravel\Console\MakeSchema;
use Symfony\Component\Console\Input\InputOption;

class MakeSchemaCommand extends MakeSchema
{
    /**
     * @var string
     */
    protected $name = 'make:schema';

    protected function getStub()
    {
        if ($this->option('non-eloquent')) {
            return $this->resolveStubPath('non-eloquent-schema.stub');
        }

        $type = $this->option('primary') ?: 'int';
        
        switch($type) {
            case 'uuid': return $this->resolveStubPath('schema.uuid.stub');
            case 'ulid': return $this->resolveStubPath('schema.ulid.stub');
            default: return $this->resolveStubPath('schema.stub');
        }
    }


    /**
     * @inheritDoc
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the schema already exists'],
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the schema applies to.'],
            ['non-eloquent', null, InputOption::VALUE_NONE, 'Create a schema for a non-Eloquent resource.'],
            ['proxy', 'p', InputOption::VALUE_NONE, 'Create a schema for an Eloquent model proxy.'],
            ['server', 's', InputOption::VALUE_REQUIRED, 'The JSON:API server the schema exists in.'],
            ['primary', '', InputOption::VALUE_OPTIONAL, 'Indicates if the generated model should use specific Primary Key Type: `int` | `uuid` | `ulid`']
        ];
        
    }
}