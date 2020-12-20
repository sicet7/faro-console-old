<?php

declare(strict_types=1);

namespace Sicet7\Faro\Console;

use DI\Invoker\FactoryParameterResolver;
use Psr\Container\ContainerInterface;
use Sicet7\Faro\Console\Exception\CommandDefinitionException;
use Sicet7\Faro\ModuleLoader as AbstractModuleLoader;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use function DI\create;
use function DI\factory;
use function DI\get;

final class ModuleLoader extends AbstractModuleLoader
{
    /**
     * @return array
     * @throws CommandDefinitionException
     */
    public function definitions(): array
    {
        $dontFactorize = [];
        $commandMasterArray = [];
        foreach ($this->getList() as $moduleFqn) {
            if (is_subclass_of($moduleFqn, ConsoleModule::class)) {
                $commandArray = call_user_func([$moduleFqn, 'getCommands']);
                $moduleDefs = call_user_func([$moduleFqn, 'getDefinitions']);
                foreach ($commandArray as $commandName => $commandClass) {
                    if (!is_subclass_of($commandClass, Command::class)) {
                        throw new CommandDefinitionException(
                            'Invalid command class. "' . $commandClass .
                            '" does not extend "' . Command::class . '".'
                        );
                    }
                    $commandMasterArray[$commandName] = $commandClass;
                    if (array_key_exists($commandClass, $moduleDefs)) {
                        $dontFactorize[] = $commandClass;
                    }
                }
            }
        }
        $definitions = [
            FactoryParameterResolver::class =>
                create(FactoryParameterResolver::class)
                    ->constructor(get(ContainerInterface::class)),
            CommandFactory::class => create(CommandFactory::class)
                ->constructor(get(FactoryParameterResolver::class)),
            CommandLoaderInterface::class => create(ContainerCommandLoader::class)
                ->constructor(get(ContainerInterface::class), $commandMasterArray),
            EventDispatcher::class => create(EventDispatcher::class),
            Application::class => function(CommandLoaderInterface $commandLoader, EventDispatcher $eventDispatcher) {
                $app = new Application();
                $app->setCommandLoader($commandLoader);
                $app->setDispatcher($eventDispatcher);
                return $app;
            },
        ];
        foreach ($commandMasterArray as $commandClass) {
            if (in_array($commandClass, $dontFactorize)) {
                continue;
            }
            $definitions[$commandClass] = factory([CommandFactory::class, 'create']);
        }
        return $definitions;
    }
}