<?php

namespace Services\NewsService;

use Services\NewsService\Interfaces\NewsServiceInterface;

class NewsService implements NewsServiceInterface
{
    /**
     * @var array
     */
    protected $news = [];

    public function __construct()
    {
        $newsBuilder = new NewsBuilder();

        $matchNews    = $newsBuilder->build(new MatchesNews());
        $transferNews = $newsBuilder->build(new TransferNews());

        $this->news = array_merge($matchNews, $transferNews);
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