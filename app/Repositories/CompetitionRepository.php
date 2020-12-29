<?php

namespace App\Repositories;

use App\Models\Club\Club;
use App\Models\Competition\Match;
use App\Models\Game\BaseClubs;
use App\Models\Game\Game;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Services\MatchService\MatchService;

class CompetitionRepository
{
    /**
     * @param int $competitionId
     *
     * @return BaseClubs[]|Collection
     */
    public function getBaseClubsByCompetition(int $competitionId)
    {
        $baseClubs = BaseClubs::all()->where('competition_id', $competitionId);
        $clubs     = [];

        foreach ($baseClubs as $club) {
            $clubs[] = $club->id;
        }

        return $clubs;
    }

    public function getClubsByCompetition(int $competition)
    {
        $result = DB::select(
            "SELECT club_id FROM competition_season WHERE competition_id = :competitionId",
            ["competitionId" => $competition]
        );

        $clubs = [];

        foreach ($result as $club) {
            $clubs[] = $club->club_id;
        }

        return $clubs;
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
        $result = Club::where('game_id', 1)->take(16)->get();

        $clubs = [];

        foreach ($result as $club) {
            $clubs[] = $club->id;
        }

        return $clubs;
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
                "clubId" => $match['hometeam_id'],
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
                "clubId" => $match['awayteam_id'],
            ]
        );
    }

    /**
     * Checks if all the games from the group stage have been played
     *
     * @param array $match
     *
     * @return bool
     */
    public function tournamentGroupsFinished(array $match)
    {
        $result = DB::select(
            "
                SELECT
                    count(tg.id) AS numberOfGroups
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

        $numberOfGames = $result[0]->numberOfGroups * 12;

        $gamesPlayed = DB::select(
            "
            SELECT COUNT(id) AS gamesPlayed
            FROM matches
            WHERE competition_id = :competitionId
            AND winner > 0
            ",
            ["competitionId" => $match["competition_id"]]
        );

        if ($numberOfGames == $gamesPlayed[0]->gamesPlayed) {
            return true;
        }

        return false;
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
                'competitionId' => $competitionId,
            ]
        );

        foreach ($teams as $team) {
            if (!isset($mappedTeams[$team->groupId])) {
                $mappedTeams[$team->groupId] = [];
            }

            $mappedTeams[$team->groupId][] = $team->id;
        }

        return $mappedTeams;
    }

    public function tournamentRoundWinner(int $matchId1, int $matchId2)
    {
        $match1 = Match::where('id', $matchId1)->first();
        $match2 = Match::where('id', $matchId2)->where('winner', '>', '0')->first();

        if (empty($match2)) {
            return false;
        }

        $team1 = new \stdClass();
        $team2 = new \stdClass();

        $team1->id     = $match1->hometeam_id;
        $team2->id     = $match1->awayteam_id;
        $team1->goals  = $match1->home_team_goals;
        $team2->goals  = $match1->away_team_goals;
        $team1->goals  += $match2->away_team_goals;
        $team2->goals  += $match2->home_team_goals;
        $team1->points = 0;
        $team2->points = 0;

        switch ($match1->winner) {
            case 1:
                $team1->points += 3;
                break;
            case 2:
                $team2->points += 3;
                break;
            case 3:
                $team1->points += 1;
                $team2->points += 1;
                break;
        }

        switch ($match2->winner) {
            case 1:
                $team2->points += 3;
                break;
            case 2:
                $team1->points += 3;
                break;
            case 3:
                $team1->points += 1;
                $team2->points += 1;
                break;
        }

        // same amount of points - checking goal difference or simulating extra time
        if ($team1->points == $team2->points) {
            if ($team1->goals == $team2->goals) {
                $matchService = new MatchService();
                return $matchService->simulateMatchExtraTime($match2->id);
            } else {
                return $team1->goals > $team2->goals ? $team1->id : $team2->id;
            }
        }

        return $team1->points > $team2->points ? $team1->id : $team2->id;
    }

    /**
     * @param int $competitionId
     *
     * @return array
     */
    public function tournamentKnockoutStageByCompetitionId(int $competitionId)
    {
        return DB::select(
            "
                SELECT * FROM tournament_knockout WHERE competition_id = :competitionId
            ",
            ['competitionId' => $competitionId]
        );
    }

    public function updateKnockoutSummary(array $summary, int $tournamentStructureId)
    {
        try {
            DB::update(
                "
                UPDATE tournament_knockout SET summary = :summary WHERE id = :id
            ",
                ['summary' => json_encode($summary), 'id' => $tournamentStructureId]
            );
        } catch (\Exception $e) {

        }
    }

    /**
     * @param int $competitionId
     *
     * @return array
     */
    public function topClubsByTournamentGroup(int $competitionId): array
    {
        return DB::select(
            "
                SELECT
                    t1.*
                FROM
                (
                    SELECT
                        id,
                        competition_id,
                        club_id,
                          points,
                        groupId,
                        @rn := IF(@prev = groupId, @rn + 1, 1) AS rn,
                        @prev := groupId
                    FROM tournament_groups
                    JOIN (SELECT @prev := NULL, @rn := 0) AS vars
                    ORDER BY groupId, points DESC
                ) AS t1
                WHERE rn <= 2
                AND competition_id = :competitionId;
            ",
            ["competitionId" => $competitionId]
        );
    }

    /**
     * @param int $competitionId
     * @param int $homeTeamId
     * @param int $points
     */
    public function updateTeamCompetitionPoints(int $competitionId, int $homeTeamId, int $points)
    {
        DB::update(
            "
                UPDATE tournament_groups
                SET `points` = `points` + :points
                WHERE competition_id = :competitionId
                AND club_id = :teamId
            ",
            [
                "points"        => $points,
                "competitionId" => $competitionId,
                "teamId"        => $homeTeamId,
            ]
        );
    }

    /**
     * @param int $competitionId
     *
     * @return array
     */
    public function finishedKnockoutMatches(int $competitionId): array
    {
        return DB::select(
            "SELECT * FROM matches WHERE competition_id = :competitionId AND winner > 0",
            ["competitionId" => $competitionId]
        );
    }

    public function resetTournamentGroupRule(int $competitionId)
    {
        DB::update(
            "
                    UPDATE competitions
                    SET groups = 0
                    WHERE id = :competitionId
                ",
            ["competitionId" => $competitionId]
        );
    }

    /**
     * @param int   $competitionId
     * @param array $summary
     */
    public function insertTournamentKnockoutSummary(int $competitionId, array $summary)
    {
        DB::insert(
            "
                INSERT INTO tournament_knockout (competition_id, summary)
                VALUES (:competitionId, :summary)
            ",
            [
                'competitionId' => $competitionId,
                'summary'       => json_encode($summary),
            ]
        );
    }

    public function insertTournamentGroups(int $competitionId, int $group, $clubId)
    {
        DB::insert(
            "
                    INSERT INTO `tournament_groups` (`competition_id`, `groupId`, `club_id`, `points`)
                    VALUES (:competitionId, :groupId, :clubId, :points)
                    ",
            [
                'competitionId' => $competitionId,
                'groupId'       => $group,
                'clubId'        => $clubId,
                'points'        => 0,
            ]
        );
    }
}
