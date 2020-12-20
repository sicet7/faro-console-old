<?php

declare(strict_types=1);

namespace Sicet7\Faro\Console;

use Symfony\Component\Console\Event\ConsoleEvent;

class EventDispatcher extends \Symfony\Component\EventDispatcher\EventDispatcher
{
    public function dispatch(object $event, string $eventName = null): object
    {
        if ($event instanceof ConsoleEvent) {
            return parent::dispatch($event, $eventName);
        }
        return $event;
    }
}