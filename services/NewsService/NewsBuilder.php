<?php

namespace Services\NewsService;

use Services\NewsService\Interfaces\NewsBuilderInterface;

class NewsBuilder
{
    public function build(NewsBuilderInterface $builder)
    {
        return $builder->checkEvents()->dispatchEvents();
    }
}