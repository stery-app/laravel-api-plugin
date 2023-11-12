<?php

namespace Stery\Api\Laravel\Http\Requests;

use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest as BaseRequest;

abstract class ResourceRequest extends BaseRequest
{

    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        if($this->isUpdating()) {
            return $this->updateRules();
        }

        if($this->isCreating()) {
            return $this->createRules();
        }

        if($this->isDeleting()) {
            return $this->deleteRules();
        }

        return [];
    }
    
    abstract function createRules();
    abstract function updateRules();
    abstract function deleteRules();

}
