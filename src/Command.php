<?php

namespace Sicet7\Faro\Console;

class Command extends \Symfony\Component\Console\Command\Command
{
    /**
     * Should be overwritten with the name of the command by extending classes.
     */
    public const COMMAND_NAME = null;

    /**
     * Command constructor.
     */
    public function __construct()
    {
        parent::__construct(static::COMMAND_NAME);
    }
}