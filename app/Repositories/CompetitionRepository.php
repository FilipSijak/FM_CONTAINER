<?php

namespace App\Repositories;

use App\Models\Club\Club;
use App\Models\Competition\Match;
use App\Models\Game\BaseClubs;
use App\Models\Game\Game;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CompetitionRepository
{
    /**
     * @param int $competitionId
     *
     * @return BaseClubs[]|Collection
     */
    public function getBaseClubsByCompetition(int $competitionId)
    {
        return BaseClubs::all()->where('competition_id', $competitionId);
    }

    public function getInitialTournamentTeamsBasedOnRanks($competition = null)
    {
        /**
         * @TODO
         *
         * This will currently take 20 clubs from the PL because those are the only one created
         * After more clubs are added to the database, only 32 of the best clubs will be added to the CL
         * for initial season. After first season, clubs will be selected based on their performance
         */

        return Club::where('game_id', 1)->take(16)->get();
    }

    /**
     * Gets all the scheduled games for all competitions which need to be simulated
     *
     * @param Game $game
     *
     * @return mixed
     */
    public function getScheduledGames(Game $game)
    {
        return Match::where('match_start', $game->game_date)->where('winner', null)->get();
    }

    /**
     * @param string $date
     * @param int    $competitionId
     *
     * @return mixed
     */
    public function getScheduledGamesForCompetition(string $date, int $competitionId)
    {
        return Match::where('match_start', $date)->where('competition_id', $competitionId)->get();
    }

    /**
     * Gets match for the game date filtered match for a game player to be played in a full match mode
     *
     * @param Game $game
     *
     * @return array
     */
    public function getUserGameIdForTheCurrentDay(Game $game)
    {
        $result = DB::select(
            "
            SELECT
                m.id
            FROM games AS g
            INNER JOIN matches AS m ON (g.id = m.game_id)
            WHERE g.user_id = :user_id
            AND g.club_id = :club_id
            AND g.id = :game_id
            AND m.match_start = :game_date
            AND (m.hometeam_id = :home_team OR m.awayteam_id = :away_team)
            ",
            [
                'game_id'   => $game->id,
                'club_id'   => $game->club_id,
                'user_id'   => $game->user_id,
                'game_date' => $game->game_date,
                'home_team' => $game->club_id,
                'away_team' => $game->club_id,
            ]
        );

        return $result;
    }

    /**
     * Takes a collection of matches and returns club names and stadium for them
     * It's being used for displaying a specified round of matches
     *
     *
     * @param Collection $matches
     *
     * @return array
     */
    public function getMatchListMetaData(Collection $matches): array
    {
        $matchListData       = [];
        $parsedMatchListData = [];

        foreach ($matches as $match) {
            $matchListData[] = $this->matchMetaData($match);
        }

        foreach ($matchListData as $data) {
            $obj = new \stdClass();

            $obj->stadiumName = $data[0]->stadium_name;
            $obj->homeTeam    = $data[0]->name;
            $obj->awayTeam    = $data[1]->name;

            $parsedMatchListData[] = (array)$obj;
        }

        return $parsedMatchListData;
    }

    /**
     * @param Match $match
     *
     * @return array
     */
    public function matchMetaData(Match $match)
    {
        $result = DB::select(
            "
            SELECT
                s.name AS stadium_name,
                c.name
            FROM (
                SELECT
                    name,
                    stadium_id
                FROM clubs
                WHERE (id = :homeTeam OR id = :awayTeam)
            ) AS c
            INNER JOIN stadiums AS s ON (s.id = :stadiumId)
            ",
            [
                'homeTeam'  => $match->hometeam_id,
                'awayTeam'  => $match->awayteam_id,
                'stadiumId' => $match->stadium_id,
            ]
        );

        return $result;
    }

    public function updateCompetitionPoints(array $match)
    {
        $homeTeamPoints = 0;
        $awayTeamPoints = 0;

        switch ($match['winner']) {
            case 1:
                $homeTeamPoints += 3;
                break;
            case 2:
                $awayTeamPoints += 3;
                break;
            case 3:
                $homeTeamPoints += 1;
                $awayTeamPoints += 1;
                break;
        }

        DB::update(
            "
                UPDATE competition_points
                SET points = points + :points
                WHERE club_id = :clubId
            ",
            [
                "points" => $homeTeamPoints,
                "clubId" => $match['hometeam_id']
            ]
        );

        DB::update(
            "
                UPDATE competition_points
                SET points = points + :points
                WHERE club_id = :clubId
            ",
            [
                "points" => $awayTeamPoints,
                "clubId" => $match['awayteam_id']
            ]
        );
    }

    public function updateTournamentSummary()
    {

    }

    public function tournamentGroupsFinished(array $match)
    {
        $result = DB::select(
            "
                SELECT
                    count(tg.id)
                FROM
                    (
                        SELECT groupId AS groupIds FROM tournament_groups
                        WHERE (club_id = :homeTeamId OR club_id = :awayTeamId)
                        LIMIT 1
                    )AS sq
                JOIN tournament_groups AS tg ON (tg.groupId = sq.groupIds)
            ",
            [
                'homeTeamId' => $match['hometeam_id'],
                'awayTeamId' => $match['awayteam_id'],
            ]
        );

        return !empty($result);
    }

    public function getTeamsMappedByTournamentGroup(int $competitionId)
    {
        $mappedTeams = [];

        $teams = DB::select(
            "
                SELECT
                    *
                FROM tournament_groups
                WHERE competition_id = :competitionId
            ",
            [
                'competitionId' => $competitionId
            ]
        );

        foreach ($teams as $team) {
            if (!isset($mappedTeams[$team->groupId])) {
                $mappedTeams[$team->groupId] = [];
            }

            $mappedTeams[$team->groupId][] = $team;
        }

        return $mappedTeams;
    }
}
