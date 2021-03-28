<?php

namespace Services\PeopleService\Interfaces;

interface PeopleServiceInterface
{
    public function createPerson(int $clubRank, int $leagueRank);

    public function setPersonConfiguration(int $personPotential, int $gameId, int $personType);

    public function generatePlayerPositionList(array $playerAttributes): array;
}