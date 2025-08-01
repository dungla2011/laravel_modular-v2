<?php

namespace Modules\Base\Exceptions;

class ResourceNotFoundException extends ModuleException
{
    public function __construct(string $resource = 'Resource')
    {
        parent::__construct("{$resource} not found", 404);
    }
}
