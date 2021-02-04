<?php

namespace App\GameEngine;

use App\Models\Competition\Competition;
use App\Models\Competition\Season;
use App\Repositories\CompetitionRepository;
use Services\CompetitionService\CompetitionService;
use Services\CompetitionService\Factories\SeasonFactory;

class SeasonStart
{
    public function __construct(int $gameId, Season $lastSeason)
    {
        $this->gameId     = $gameId;
        $this->lastSeason = $lastSeason;
    }

    public function processSeasonStart()
    {
        $seasonFactory = new SeasonFactory();
        $startDate     = date('Y-m-d', strtotime($this->lastSeason->end_date));
        $endDate       = date('Y-m-d', strtotime('+1 year', strtotime($startDate)));
        $season        = $seasonFactory->make($this->gameId, $startDate, $endDate);

        $season->save();
        $clubsByCompetitions = $this->reorderClubsByCompetitions();

        $this->mapTablesForNewSeason($season->id, $clubsByCompetitions);
        $this->setNewSeasonCompetitionsMatches($clubsByCompetitions, $season);
        $this->updateClubsFinances();
    }

    /**
     * @return array
     */
    private function reorderClubsByCompetitions(): array
    {
        $competitions          = Competition::all();
        $competitionRepository = new CompetitionRepository();
        // map clubs by competition
        $clubsByCompetition = [];

        // unset relegated clubs
        // push promoted clubs
        foreach ($competitions as $competition) {
            if ($competition->type == 'league') {
                $clubsByCompetition[$competition->id] = $competitionRepository->getClubsByCompetition($competition->id);
                $competitionHierarchy                 = $competitionRepository->getCompetitionHierarchy($competition->id);

                if (isset($competitionHierarchy[0])) {
                    // if it's a country top division
                    if (!$competitionHierarchy[0]->parent_competition_id) {
                        $relegatedClubs = $competitionRepository->getRelegatedClubsByCompetition($competition->id, $this->lastSeason->id);
                        foreach ($relegatedClubs as $club) {
                            unset($clubsByCompetition[$competition->id][$club->club_id]);
                        }

                        $promotedFromLowerDivision = $competitionRepository->getPromotedClubsByCompetition($competitionHierarchy[0]->child_competition_id, $this->lastSeason->id);

                        foreach ($promotedFromLowerDivision as $club) {
                            $clubsByCompetition[$competition->id][$club->club_id] = $club->club_id;
                        }
                    } elseif (!$competitionHierarchy[0]->child_competition_id) {
                        // if there is no lower division than the current one, we only promote clubs
                        $relegatedFromUpperDivision = $competitionRepository->getRelegatedClubsByCompetition($competitionHierarchy[0]->parent_competition_id, $this->lastSeason->id);
                        foreach ($relegatedFromUpperDivision as $club) {
                            $clubsByCompetition[$competition->id][$club->club_id] = $club->club_id;
                        }

                        $clubsForPromotion = $competitionRepository->getPromotedClubsByCompetition($competition->id, $this->lastSeason->id);

                        foreach ($clubsForPromotion as $club) {
                            unset($clubsByCompetition[$competition->id][$club->club_id]);
                        }
                    } elseif ($competitionHierarchy[0]->child_competition_id && $competitionHierarchy[0]->parent_competition_id) {
                        // there is both higher and lower division
                        // clubs are being promoted and relegated
                        $relegatedClubs = $competitionRepository->getRelegatedClubsByCompetition($competition->id, $this->lastSeason->id);
                        foreach ($relegatedClubs as $club) {
                            unset($clubsByCompetition[$competition->id][$club->club_id]);
                        }

                        $clubsForPromotion = $competitionRepository->getPromotedClubsByCompetition($competition->id, $this->lastSeason->id);

                        foreach ($clubsForPromotion as $club) {
                            unset($clubsByCompetition[$competition->id][$club->club_id]);
                        }

                        $promotedFromLowerDivision  = $competitionRepository->getPromotedClubsByCompetition($competitionHierarchy[0]->child_competition_id, $this->lastSeason->id);
                        $relegatedFromUpperDivision = $competitionRepository->getRelegatedClubsByCompetition($competitionHierarchy[0]->parent_competition_id, $this->lastSeason->id);

                        foreach ($promotedFromLowerDivision as $club) {
                            $clubsByCompetition[$competition->id][$club->club_id] = $club->club_id;
                        }

                        foreach ($relegatedFromUpperDivision as $club) {
                            $clubsByCompetition[$competition->id][$club->club_id] = $club->club_id;
                        }
                    } else {
                        // single division within a country
                    }
                }
            } elseif ($competition->type == 'league' && $competition->groups) {
                // tournament groups - european competitions
            } else {
                // tournament knockout - domestic cups
            }
        }

        return $clubsByCompetition;
    }

    /**
     * @param int   $seasonId
     * @param array $competitionClubs
     */
    private function mapTablesForNewSeason(int $seasonId, array $competitionClubs)
    {
        $competitionRepository = new CompetitionRepository();

        $competitionRepository->bulkSeasonPointsInsert($seasonId, $competitionClubs);
        $competitionRepository->bulkCompetitionsSeasonsInsert($seasonId, $competitionClubs);
    }

    /**
     * @param array  $clubsByCompetitions
     * @param Season $season
     */
    private function setNewSeasonCompetitionsMatches(array $clubsByCompetitions, Season $season)
    {
        foreach ($clubsByCompetitions as $competitionId => $clubs) {
            $competition        = Competition::where('id', $competitionId)->get();
            $competitionService = new CompetitionService();

            if ($competition->type = 'league') {
                $competitionService->makeLeague($clubs, $competitionId, $season->id, $season->start_date);
            } elseif ($competition->type == 'tournament' && $competition->groups) {
                $competitionService->makeTournamentGroupStage($clubs, $competitionId, $season->id, $season->start_date);
            } else {
                $competitionService->makeTournament($clubs, $competitionId, $season->id, $season->start_date);
            }
        }
    }

    private function updateClubsFinances()
    {
        //prize money...
    }
}
