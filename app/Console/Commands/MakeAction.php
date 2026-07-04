<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeAction extends GeneratorCommand
{
    protected $name = 'make:action';

    protected $description = 'Create a new action class (single write operation, consumes a DTO)';

    protected $type = 'Action';

    protected function getStub(): string
    {
        return base_path('stubs/action.stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Actions';
    }
}
