<?php

namespace Services\NewsService;

use Services\NewsService\Interfaces\NewsBuilderInterface;

class MatchesNews implements NewsBuilderInterface
{
    protected $events = [];

    public function checkEvents()
    {
        // TODO: Implement checkEvents() method.
        $event          = new \stdClass();
        $event->title   = 'general title 1';
        $event->content = 'general content 1';

        $this->events[] = $event;

        return $this;
    }

    public function dispatchEvents()
    {
        // TODO: Implement dispatchEvents() method.

        return $this->events;
    }
}