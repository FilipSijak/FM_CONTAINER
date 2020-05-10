<?php

namespace Services\NewsService;

use Services\NewsService\Interfaces\NewsBuilderInterface;

class TransferNews implements NewsBuilderInterface
{
    protected $events = [];

    public function checkEvents()
    {
        // TODO: Implement checkEvents() method.
        $this->events[] = 'event  transfer1';

        return $this;
    }

    public function dispatchEvents()
    {
        // TODO: Implement dispatchEvents() method.

        return $this->events;
    }
}