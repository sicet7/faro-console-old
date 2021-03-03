<?php

namespace Sicet7\Faro\Console\Event;

interface ListenerInterface
{
    public function execute(object $event);
}