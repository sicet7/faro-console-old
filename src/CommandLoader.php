<?php

namespace Sicet7\Faro\Console;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

class CommandLoader extends ContainerCommandLoader
{
    public function __construct(ContainerInterface $container, array $commandList)
    {
        $commandMap = [];
        foreach ($commandList as $commandFqn) {
            if (!is_subclass_of($commandFqn, Command::class)) {
                continue;
            }
            $commandMap[$commandFqn::COMMAND_NAME] = $commandFqn;
        }
        parent::__construct($container, $commandMap);
    }
}