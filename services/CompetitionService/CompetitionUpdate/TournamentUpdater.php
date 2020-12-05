<?php

namespace Services\CompetitionService\CompetitionUpdate;

use App\Factories\Competition\MatchFactory;
use App\Models\Competition\Match;
use App\Models\Schema\KnockoutSummary;
use App\Repositories\CompetitionRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Parsers\KnockoutSummaryParser;
use Services\CompetitionService\CompetitionService;
use Services\MatchService\MatchService;

class TournamentUpdater
{
    /**
     * @var array
     */
    private $matches;

    /**
     * @var CompetitionRepository
     */
    private $competitionRepository;

    public function __construct()
    {
        $this->competitionRepository = new CompetitionRepository();
    }


    public function setMatches(array $matches)
    {
        $this->matches = $matches;

        return $this;
    }

    public function updatePointsTable()
    {
        if ($this->competitionRepository->tournamentGroupsFinished($this->matches[0])) {
            return $this->updateTournamentSummary();
        }

        // update group tables

        return $this;
    }

    /**
     * Updates the knockout stage summary
     *
     * @return $this
     */
    public function updateTournamentSummary()
    {
        $competitionId       = $this->matches[0]['competition_id'];
        $tournamentStructure = $this->competitionRepository->tournamentKnockoutStageByCompetitionId($competitionId)[0];
        $summary             = json_decode($tournamentStructure->summary, true);

        $knockoutSummary  = new KnockoutSummary();
        $tournamentParser = new KnockoutSummaryParser();
        $tournamentParser->parseSchema($summary, $knockoutSummary);

        $lastRound = $summary["first_group"]["num_rounds"];

        if (
            !$summary["winner"] &&
            !$summary["finals_match"]
        ) {
            $summary["first_group"]["rounds"]  = $this->updateTournamentGroup($knockoutSummary->getFirstGroup()["rounds"]);
            $summary["second_group"]["rounds"] = $this->updateTournamentGroup($knockoutSummary->getSecondGroup()["rounds"]);
        }

        if (
            !$summary["winner"] &&
            isset($summary["first_group"]["rounds"][$lastRound]["pairs"][0]) &&
            isset($summary["second_group"]["rounds"][$lastRound]["pairs"][0]) &&
            $summary["first_group"]["rounds"][$lastRound]["pairs"][0]["winner"] &&
            !$summary["finals_match"]
        ) {
            $firstGroupPair  = $summary["first_group"]["rounds"][$lastRound]["pairs"][0];
            $secondGroupPair = $summary["first_group"]["rounds"][$lastRound]["pairs"][0];

            $firstGroupWinner  = $firstGroupPair["winner"];
            $secondGroupWinner = $secondGroupPair["winner"];

            // takes the last game of previous round to decide on the date of finals match
            $lastMatch  = Match::where('id', $firstGroupPair["match2Id"])->first();
            $match      = new MatchFactory();
            $finalsDate = Carbon::create($lastMatch->match_start)->addWeek(2);

            // create finals match
            $summary["finals_match"] = $match->make(
                $lastMatch->game_id,
                $lastMatch->competition_id,
                $firstGroupWinner,
                $secondGroupWinner,
                $finalsDate
            )->id;
        }

        if ($summary["finals_match"] == $this->matches[0]["id"]) {
            $finalsMatch = $this->matches[0];

            if ($finalsMatch["winner"] == 3) {
                $matchService = new MatchService();
                $matchService->simulateMatchExtraTime($summary["finals_match"]);
            }

            $summary["winner"] = $finalsMatch["winner"] == 1 ? $finalsMatch["hometeam_id"] : $finalsMatch["awayteam_id"];
        }

        $this->competitionRepository->updateKnockoutSummary($summary, $tournamentStructure->id);

        return $this;
    }

    /**
     * Gets an array that represents the tournament
     * Goes through each round and checks if round is completed, creates new rounds and matches for it
     *
     * @param array $tournamentGroup
     *
     * @return array
     */
    public function updateTournamentGroup(array $tournamentGroup)
    {
        $finishedMatches = DB::select(
            "SELECT * FROM matches WHERE competition_id = 7 AND winner > 0"
        );

        foreach ($finishedMatches as $match) {
            $matchesMapped[$match->id] = $match->id;
        }

        $winners = [];

        foreach ($tournamentGroup as $key => &$round) {
            $winners[$key] = [];

            if (!empty($round["pairs"])) {
                // already created pairs
                // will take winners from them and create new matches in else of those winners

                // going through each round that has assigned pairs and gives a winner
                foreach ($round["pairs"] as &$pair) {
                    if ($pair["winner"]) {
                        continue;
                    }

                    if (isset($matchesMapped[$pair["match1Id"]]) && isset($matchesMapped[$pair["match2Id"]])) {
                        $winner = $this->competitionRepository->tournamentRoundWinner($pair["match1Id"], $pair["match2Id"]);

                        if (!$winner) {
                            break;
                        }

                        $pair["winner"]  = $winner;
                        $winners[$key][] = $winner;
                    } else {
                        // means that only one match has been played in each round so no need to go through all of them
                        // this will change once matches of the same round are played on different dates
                        //break;
                    }
                }
            } else {
                // creates new set of pairs after previous round has winners

                if (
                    (isset($tournamentGroup[$key - 1]) && $tournamentGroup[$key - 1]["pairs"][0]["winner"]) &&
                    empty($tournamentGroup[$key]["pairs"])
                ) {
                    $competitionService = new CompetitionService();
                    $competitionService->setClubs($winners[$key - 1]);
                    $newMatches = $competitionService->tournamentNewRound();
                    $newMatches = json_decode(json_encode($newMatches), true);
                    $this->createNewRoundMatches($newMatches, Match::where('id', $tournamentGroup[$key - 1]["pairs"][0]["match2Id"])->first());
                    $tournamentGroup[$key]["pairs"] = $newMatches;

                    break;
                } else {
                    // other rounds are still empty so no need to loop through them

                    break;
                }
            }
        }

        return $tournamentGroup;
    }

    /**
     * @param array $pairs
     * @param       $lastMatch
     */
    private function createNewRoundMatches(array &$pairs, $lastMatch)
    {
        $match           = new MatchFactory();
        $firstMatchDate  = Carbon::create($lastMatch->match_start)->addWeek();
        $secondMatchDate = Carbon::create($lastMatch->match_start)->addWeek(2);

        foreach ($pairs as &$pair) {
            $pair["match1Id"] = $match->make(
                $lastMatch->game_id,
                $lastMatch->competition_id,
                $pair["match1"]["homeTeamId"],
                $pair["match1"]["awayTeamId"],
                $firstMatchDate
            )->id;

            $pair["match2Id"] = $match->make(
                $lastMatch->game_id,
                $lastMatch->competition_id,
                $pair["match2"]["homeTeamId"],
                $pair["match2"]["awayTeamId"],
                $secondMatchDate
            )->id;
        }
    }
}
