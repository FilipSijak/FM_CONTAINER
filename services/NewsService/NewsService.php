<?php

namespace Services\NewsService;

use Services\NewsService\Interfaces\NewsServiceInterface;

class NewsService implements NewsServiceInterface
{
    protected $news = [];

    public function __construct(
    )
    {
        $newsBuilder = new NewsBuilder();

        $this->news[] = $newsBuilder->build(new MatchesNews());
        $this->news[] = $newsBuilder->build(new TransferNews());
    }

    public function getAllUpdates()
    {
        // matches reader

        //transfers reader
    }
}