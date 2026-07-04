<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeDto extends GeneratorCommand
{
    protected $name = 'make:dto';

    protected $description = 'Create a new DTO class (immutable, validated data crossing layer boundaries)';

    protected $type = 'DTO';

    protected function getStub(): string
    {
        return base_path('stubs/dto.stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\DTOs';
    }
}
