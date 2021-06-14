<?php

namespace Services\TransferService\TransferWindowConfig;

use App\Models\Game\Game;
use Carbon\Carbon;

class TransferWindowAvailability
{
    private $game;

    public function __construct(int $gameId)
    {
        $this->game = Game::find($gameId);
    }

    private function currentTransferWindow()
    {
        $currentYear = Carbon::createFromFormat('Y-m-d', $this->game->game_date)->year;

        return (
            $this->game->game_date >= $currentYear . '-' . TransferWindowConfig::SUMMER_WINDOW_START &&
            $this->game->game_date <= $currentYear . '-' . TransferWindowConfig::SUMMER_WINDOW_END
        ) || (
            $this->game->game_date >= $currentYear . '-' . TransferWindowConfig::WINTER_WINDOW_START &&
            $this->game->game_date <= $currentYear . '-' . TransferWindowConfig::WINTER_WINDOW_END
        );
    }

    public function getTransferAvailabilityDate()
    {
        $currentTransferWindow = $this->currentTransferWindow();
        $currentYear = Carbon::createFromFormat('Y-m-d', $this->game->game_date)->year;

        if (!$currentTransferWindow) {
            if ($this->game->game_date > $currentYear . '-' . TransferWindowConfig::SUMMER_WINDOW_END) {
                $nextYear = $currentYear + 1;

                return $nextYear . '-' . TransferWindowConfig::WINTER_WINDOW_START;
            } else {
                return $currentYear . '-' . TransferWindowConfig::SUMMER_WINDOW_START;
            }
        } else {
            return $this->game->game_date;
        }
    }
}