<?php

namespace App\Repositories;

use App\Factories\News\NewsFactory;
use App\Repositories\Interfaces\NewsRepositoryInterface;

class NewsRepository implements NewsRepositoryInterface
{
    public function getCurrentNews()
    {

    }

    public function storeGeneratedNews(int $gameId, array $news)
    {
        $newsFactory = new NewsFactory();

        foreach ($news as $item) {
            $newsFactory->create(1, $item->title, $item->content);
        }
    }
}