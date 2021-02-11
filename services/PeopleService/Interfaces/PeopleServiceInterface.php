<?php

namespace Services\PeopleService\Interfaces;

interface PeopleServiceInterface
{
    public function createPerson();

    public function setPersonConfiguration(int $personPotential, int $gameId, int $personType);

    public function generatePlayerPositionList(array $playerAttributes): array;
}