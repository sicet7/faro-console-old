<?php

declare(strict_types=1);

namespace Sicet7\Faro\Console;

use DI\Invoker\FactoryParameterResolver;
use Psr\Container\ContainerInterface;
use Sicet7\Faro\ModuleContainer as AbstractModuleContainer;
use Sicet7\Faro\ModuleLoader as CoreModuleLoader;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use function DI\create;
use function DI\get;

final class ModuleContainer extends AbstractModuleContainer
{
    /**
     * @return array
     */
    public function definitions(): array
    {
        return [
            FactoryParameterResolver::class =>
                create(FactoryParameterResolver::class)
                    ->constructor(get(ContainerInterface::class)),
            CommandFactory::class => create(CommandFactory::class)
                ->constructor(get(FactoryParameterResolver::class)),
            EventDispatcher::class => create(EventDispatcher::class),
            Application::class => function(CommandLoaderInterface $commandLoader, EventDispatcher $eventDispatcher) {
                $app = new Application();
                $app->setCommandLoader($commandLoader);
                $app->setDispatcher($eventDispatcher);
                return $app;
            },
        ];
    }

    protected function loadDefinitions()
    {
        parent::loadDefinitions();
        $commandFqns = [];
        foreach ($this->getList() as $moduleLoader) {
            if (!($moduleLoader instanceof ModuleLoader)) {
                continue;
            }
            $commandFqns = array_merge($commandFqns, $moduleLoader->getCommandFqns());
        }
        $this->getContainerBuilder()->addDefinitions([
            CommandLoaderInterface::class => create(CommandLoader::class)
                ->constructor(get(ContainerInterface::class), $commandFqns)
        ]);
    }

    public function createLoader(string $moduleFqn): CoreModuleLoader
    {
        return new ModuleLoader($moduleFqn, $this);
    }
}