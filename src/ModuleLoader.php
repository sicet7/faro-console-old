<?php

namespace Sicet7\Faro\Console;

use Sicet7\Faro\ModuleLoader as CoreModuleLoader;
use function DI\factory;

class ModuleLoader extends CoreModuleLoader
{
    private array $commandFqns = [];

    public function getDefinitions(): array
    {
        $definitions = parent::getDefinitions();
        foreach ($definitions as $key => $def) {
            $fqn = $this->getCommandFqn($key, $def);
            if ($fqn === null) {
                continue;
            }
            unset($definitions[$key]);
            $definitions[$fqn] = factory([CommandFactory::class, 'create']);
            $this->commandFqns[] = $fqn;
        }
        return $definitions;
    }

    /**
     * @return array
     */
    public function getCommandFqns(): array
    {
        return $this->commandFqns;
    }

    /**
     * @param mixed $key
     * @param mixed $def
     * @return string|null
     */
    private function getCommandFqn($key, $def): ?string
    {
        if (!is_numeric($key) && is_subclass_of($key, Command::class) && !is_callable($def)) {
            return $key;
        }
        if (is_numeric($key) && is_subclass_of($def, Command::class)) {
            return $def;
        }
        return null;
    }
}