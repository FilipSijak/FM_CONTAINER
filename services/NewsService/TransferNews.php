<?php

namespace Services\NewsService;

use Services\NewsService\Interfaces\NewsBuilderInterface;

class TransferNews implements NewsBuilderInterface
{
    protected $events = [];

    public function checkEvents()
    {
        // TODO: Implement checkEvents() method.
        $event          = new \stdClass();
        $event->title   = 'transfer title 1';
        $event->content = 'transfer content 1';

        $this->events[] = $event;

        return $this;
    }

    public function dispatchEvents()
    {
        // TODO: Implement dispatchEvents() method.

        return $this->events;
    }
}