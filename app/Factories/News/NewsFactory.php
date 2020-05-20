<?php

namespace App\Factories\News;

use App\Models\Game\News;

/**
 * Class NewsFactory
 *
 * @package App\Factories\News
 */
class NewsFactory
{
    public function create(
        int $gameId,
        string $title,
        string $content
    ) {
        $news = new News();

        $news->game_id = $gameId;
        $news->title   = $title;
        $news->content = $content;

        $news->save();
    }
}