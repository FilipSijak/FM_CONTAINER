<?php

namespace App\Factories\Competition;

use App\Models\Competition\Season;
use App\Repositories\Interfaces\SeasonRepositoryInterface;
use Carbon\Carbon;

class SeasonFactory
{
    /**
     * @var SeasonRepositoryInterface
     */
    protected $seasonRepository;

    public function __construct(SeasonRepositoryInterface $seasonRepository)
    {
        $this->seasonRepository = $seasonRepository;
    }

    public function make(int $gameId)
    {
        $season = new Season();

        $season->game_id = $gameId;
        $season->start_date = date('Y-m-d', strtotime(date("Y") . '-06-01'));
        $season->end_date = date('Y-m-d', strtotime('+1 year', strtotime($season->start_date)));
    }
}