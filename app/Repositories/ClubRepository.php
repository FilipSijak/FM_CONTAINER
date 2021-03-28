<?php

namespace App\Repositories;

use App\Models\Club\Club;
use App\Repositories\Interfaces\ClubRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ClubRepository implements ClubRepositoryInterface
{
    public function getAllClubsByGame(int $gameId)
    {
        return Club::where('game_id', $gameId);
    }

    public function getInjuryList($gameId, $clubId)
    {

    }

    public function getRetiringPlayers($gameId, $clubId)
    {
        return [];
    }

    public function getActiveTransferBids()
    {

    }

    /**
     * @param int $clubId
     * @param int $seasonId
     *
     * @return array
     */
    public function getLeagueByClub(int $clubId, int $seasonId): array
    {
        $resultSet = DB::select(
            "
                SELECT *
                FROM competition_season AS cs
                INNER JOIN competitions AS c ON (c.id = cs.competition_id)
                WHERE club_id = :clubId
                AND season_id = :seasonId
                AND c.`type` = 'league'
            ",
            ["clubId" => $clubId, 'seasonId' => $seasonId]
        );

        return !empty($resultSet) ? (array)$resultSet[0] : [];
    }
}