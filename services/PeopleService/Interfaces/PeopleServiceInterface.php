<?php

namespace Services\PeopleService\Interfaces;

interface PeopleServiceInterface
{
    public function createPerson(int $clubRank, int $leagueRank);

    public function setPersonConfiguration(\stdClass $personConfig, int $gameId, string $personType);

    public function generatePlayerPositionList(array $playerAttributes): array;
}