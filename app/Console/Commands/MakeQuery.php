<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeQuery extends GeneratorCommand
{
    protected $name = 'make:query';

    protected $description = 'Create a new query class (single read operation, the only layer that knows the schema)';

    protected $type = 'Query';

    protected function getStub(): string
    {
        return base_path('stubs/query.stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Queries';
    }
}
