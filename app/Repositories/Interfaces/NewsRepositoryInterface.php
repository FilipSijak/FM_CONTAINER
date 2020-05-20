<?php

namespace App\Repositories\Interfaces;

interface NewsRepositoryInterface
{
    public function getCurrentNews();

    public function storeGeneratedNews(int $gameId, array $news);
}