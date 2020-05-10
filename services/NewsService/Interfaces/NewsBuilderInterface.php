<?php

namespace Services\NewsService\Interfaces;

interface NewsBuilderInterface
{
    public function checkEvents();

    public function dispatchEvents();
}