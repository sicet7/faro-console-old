<?php

declare(strict_types=1);

namespace Sicet7\Faro\Console;

use DI\DependencyException;
use DI\Factory\RequestedEntry;
use Invoker\ParameterResolver\ParameterResolver;
use Symfony\Component\Console\Command\Command;

class CommandFactory
{
    /**
     * @var ParameterResolver
     */
    private ParameterResolver $resolver;

    public function __construct(ParameterResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param RequestedEntry $entry
     * @param string $name
     * @return Command
     * @throws DependencyException
     */
    public function create(RequestedEntry $entry, string $name): Command
    {
        $entryName = $entry->getName();
        if (!is_subclass_of($entryName, Command::class)) {
            throw new DependencyException('"' . self::class . '" cannot instantiate class "' . $entryName . '".');
        }
        try {
            $args = $this->resolver->getParameters(
                new \ReflectionMethod($entryName, '__construct'),
                [
                    'name' => $name
                ],
                []
            );
            ksort($args);
            return new $entryName(...$args);
        } catch (\ReflectionException $exception) {
            throw new DependencyException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}