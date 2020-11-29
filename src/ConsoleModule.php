<?php

declare(strict_types=1);

namespace Sicet7\Faro\Console;

use Sicet7\Faro\AbstractModule;

abstract class ConsoleModule extends AbstractModule
{
    public static function getCommands(): array
    {
        return [];
    }
}