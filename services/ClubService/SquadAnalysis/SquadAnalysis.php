<?php

namespace Services\ClubService\SquadAnalysis;

class SquadAnalysis
{
    public function __construct(array $playerList)
    {
        $this->playerList = $playerList;
    }

    public function squadAnalysisResult()
    {
        $playersByPosition = [];

        foreach ($this->playerList as $key => $player) {
            echo $player->position;
            echo "<br>/";
            if (!isset($playersByPosition[$player->position])) {
                $playersByPosition[$player->position] = [];
            }

            $playersByPosition[$player->position][] = $player->id;
        }

        dd(1);
    }

    private function analyseDepthByPosition()
    {

    }

    private function analyseQualityByPosition()
    {

    }
}
