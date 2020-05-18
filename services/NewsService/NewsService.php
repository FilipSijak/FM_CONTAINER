<?php

namespace Services\NewsService;

use Services\NewsService\Interfaces\NewsServiceInterface;

class NewsService implements NewsServiceInterface
{
    /**
     * @var array
     */
    protected $news = [];

    public function __construct(
    )
    {
        $newsBuilder = new NewsBuilder();

        $this->news[] = $newsBuilder->build(new MatchesNews());
        $this->news[] = $newsBuilder->build(new TransferNews());
    }

    public function getNews()
    {
        return $this->news;
    }

    public function getAllUpdates()
    {
        // matches reader

        //transfers reader
    }
}