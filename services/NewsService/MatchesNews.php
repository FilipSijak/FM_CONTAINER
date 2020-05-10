<?php

namespace Services\NewsService;

use Services\NewsService\Interfaces\NewsBuilderInterface;

class MatchesNews implements NewsBuilderInterface
{
    protected $events = [];

    public function checkEvents()
    {
        // TODO: Implement checkEvents() method.
        $this->events[] = 'event 1';

        return $this;
    }

    public function dispatchEvents()
    {
        // TODO: Implement dispatchEvents() method.

        return $this->events;
    }
}