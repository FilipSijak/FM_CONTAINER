<?php

namespace Services\CompetitionService\Interfaces;

interface CompetitionServiceInterface
{
    public function makeLeague(array $clubs, int $competitionId, int $seasonId, string $date);

    public function makeTournament(array $clubs, int $competitionId, int $seasonId, string $date);
}
