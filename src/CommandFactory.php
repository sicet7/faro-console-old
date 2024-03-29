<?php

declare(strict_types=1);

namespace Sicet7\Faro\Console;

use DI\DependencyException;
use DI\Factory\RequestedEntry;
use Invoker\ParameterResolver\ParameterResolver;
use Symfony\Component\Console\Command\Command;

class CommandFactory extends GenericFactory
{
    private array $providedParameters = [];

    /**
     * @param RequestedEntry $entry
     * @param string|null $name
     * @return object|Command
     * @throws DependencyException
     */
    public function create(RequestedEntry $entry, string $name = null): object
    {
        $this->providedParameters = [];
        if (!empty($name)) {
            $this->providedParameters['name'] = $name;
        }
        return parent::create($entry);
    }

    protected function getProvidedParameters(): array
    {
        return $this->providedParameters;
    }

    protected function getResolvedParameters(): array
    {
        return [];
    }

    protected function getClassWhitelist(): array
    {
        return [
            Command::class
        ];
    }
}