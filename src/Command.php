<?php

namespace Sicet7\Faro\Console;

class Command extends \Symfony\Component\Console\Command\Command
{
    /**
     * can be overwritten with the name of the command by extending classes.
     */
    public const COMMAND_NAME = null;

    /**
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        if (static::COMMAND_NAME !== null) {
            return static::COMMAND_NAME;
        }
        return parent::getDefaultName();
    }
}