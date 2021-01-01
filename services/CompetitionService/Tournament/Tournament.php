<?php

namespace Services\CompetitionService\Tournament;

use Services\MatchService\Factories\MatchFactory;
use App\Repositories\CompetitionRepository;
use Services\CompetitionService\CompetitionsConfig\TournamentConfig;
use Services\CompetitionService\CompetitionService;
use Services\CompetitionService\League\League;

class Tournament
{
    /**
     * @var array
     */
    private $summary = [];
    /**
     * @var CompetitionService
     */
    private $competitionService;
    /**
     * @var CompetitionRepository
     */
    private $competitionRepository;
    private $clubs;

    /**
     * Tournament constructor.
     *
     * @param $clubs
     */
    public function __construct($clubs)
    {
        $this->clubs                 = $clubs;
        $this->competitionService    = new CompetitionService();
        $this->competitionRepository = new CompetitionRepository();
    }

    public function createTournament(): Tournament
    {
        $clubsCount = count($this->clubs);
        $halfSize   = ($clubsCount / 2);
        $rounds     = 0;

        $calcNumRounds = function ($n) use (&$calcNumRounds, &$rounds) {
            if ($n % 2 == 1) {
                return 1;
            }

            $rounds++;

            return $calcNumRounds($n / 2);
        };


        $calcNumRounds($halfSize);

        $this->summary = [
            "first_group"       => [
                "num_rounds" => $rounds,
                "rounds"     => [],
            ],
            "second_group"      => [
                "num_rounds" => $rounds,
                "rounds"     => [],
            ],
            "winner"            => null,
            "second_placed"     => null,
            "third_placed"      => null,
            "finals_match"      => null,
            "third_place_match" => null,
        ];

        for ($i = 1; $i <= $rounds; $i++) {
            $this->summary["first_group"]["rounds"][$i]  = ["pairs" => []];
            $this->summary["second_group"]["rounds"][$i] = ["pairs" => []];
        }

        for ($i = 0, $k = $clubsCount - 1; $i < $halfSize; $i++, $k--) {
            $pair = $this->makePairMatches($this->clubs[$i], $this->clubs[$k]);

            // half of the pairs go into one group, the other half into a second group
            if ($i < $halfSize / 2) {
                $this->summary["first_group"]["rounds"][1]["pairs"][] = $pair;
            } else {
                $this->summary["second_group"]["rounds"][1]["pairs"][] = $pair;
            }
        }

        return $this;
    }

    /**
     * @param int $firstTeamId
     * @param int $secondTeamId
     *
     * @return \stdClass
     */
    private function makePairMatches(int $firstTeamId, int $secondTeamId)
    {
        $pair   = new \stdClass();
        $match1 = new \stdClass();
        $match2 = new \stdClass();

        $match1->homeTeamId = $firstTeamId;
        $match1->awayTeamId = $secondTeamId;
        $match2->homeTeamId = $secondTeamId;
        $match2->awayTeamId = $firstTeamId;

        $pair->match1   = $match1;
        $pair->match2   = $match2;
        $pair->winner   = null;
        $pair->match1Id = null;
        $pair->match2Id = null;

        return $pair;
    }

    /**
     * @return array
     */
    public function setNextRoundPairs(): array
    {
        $clubsCount = count($this->clubs);
        $halfSize   = ($clubsCount / 2);
        $pairs      = [];

        for ($i = 0, $k = $clubsCount - 1; $i < $halfSize; $i++, $k--) {
            $pairs[] = $this->makePairMatches($this->clubs[$i], $this->clubs[$k]);
        }

        return $pairs;
    }

    /**
     * @param int  $competitionId
     * @param bool $startDate
     */
    public function populateTournamentFixtures(int $competitionId, $startDate = false)
    {
        $matchFactory     = new MatchFactory();
        $tournamentConfig = new TournamentConfig();

        if (!$startDate) {
            $firstGame  = $tournamentConfig->getStartDate()->copy()->modify("next Tuesday");
            $secondGame = $firstGame->copy()->addWeek();
        } else {
            $firstGame  = $tournamentConfig->getWinterKnockoutStartDate()->copy()->modify("next Tuesday");
            $secondGame = $firstGame->copy()->addWeek();
        }


        $firstRoundPairs = array_merge(
            $this->summary["first_group"]["rounds"][1]["pairs"],
            $this->summary["second_group"]["rounds"][1]["pairs"]
        );

        foreach ($firstRoundPairs as $pair) {
            $match1 = $matchFactory->make(
                $competitionId,
                $pair->match1->homeTeamId,
                $pair->match1->awayTeamId,
                $firstGame
            );

            $pair->match1Id = $match1->id;

            $match2 = $matchFactory->make(
                $competitionId,
                $pair->match2->homeTeamId,
                $pair->match2->awayTeamId,
                $secondGame
            );

            $pair->match2Id = $match2->id;
        }

        $this->competitionRepository->insertTournamentKnockoutSummary($competitionId, $this->summary);
    }

    /**
     * @param array $clubs
     * @param int   $competitionId
     * @param int   $seasonId
     */
    public function setTournamentGroups(array $clubs, int $competitionId, int $seasonId)
    {
        $tournamentConfig = new TournamentConfig();
        $this->populateTournamentGroups($competitionId);
        $mappedTeams = $this->competitionRepository->getTeamsMappedByTournamentGroup($competitionId);

        foreach ($mappedTeams as $group => $teams) {
            $carbonCopy     = $tournamentConfig->getStartDate()->copy();
            $firstRoundDate = $carbonCopy->modify("next Tuesday");
            $league         = new League($teams, $competitionId, $seasonId);
            $leagueFixtures = $league->generateLeagueGames();

            $league->populateLeagueFixtures($leagueFixtures, $competitionId, $firstRoundDate, 2);
        }
    }

    /**
     * @param int $competitionId
     */
    public function populateTournamentGroups(int $competitionId)
    {
        $competitionRepository = new CompetitionRepository();
        $clubsByCompetition    = $competitionRepository->getInitialTournamentTeamsBasedOnRanks();
        $counter               = 0;
        $currentGroup          = '';

        $groups = [
            0  => 1,
            4  => 2,
            8  => 3,
            12 => 4,
            16 => 5,
            20 => 6,
        ];

        for ($i = 0; $i < count($clubsByCompetition); $i++) {
            if (isset($groups[$counter])) {
                $currentGroup = $groups[$counter];
            }

            try {
                $this->competitionRepository->insertTournamentGroups($competitionId, $currentGroup, $clubsByCompetition[$i]);
            } catch (\Exception $e) {
                // @TODO
                dd($e);
            }

            $counter++;
        }
    }
}