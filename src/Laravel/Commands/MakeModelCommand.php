<?php

namespace Stery\Api\Laravel\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeModelCommand extends ModelMakeCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'jsonapi:model';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'JSON:API Model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new JSON:API model';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */ 
    protected function getStub()
    {
        
        if ($this->option('pivot')) {
            return $this->resolveStubPath('/stubs/model.pivot.stub');
        }
        
        if ($this->option('morph-pivot')) {
            return $this->resolveStubPath('/stubs/model.morph-pivot.stub');
        }

        $type = $this->option('primary') ?: 'int';

        switch($type) {
            case 'uuid': return $this->resolveStubPath('/stubs/model.uuid.stub');
            case 'ulid': return $this->resolveStubPath('/stubs/model.ulid.stub');
            default: return $this->resolveStubPath('/stubs/model.stub');
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        $options[] = ['primary', '', InputOption::VALUE_OPTIONAL, 'Indicates if the generated model should use specific Primary Key Type: `int` | `uuid` | `ulid`'];
        
        return $options;
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return __DIR__.$stub;
    }

}