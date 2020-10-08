<?php

namespace Services\PeopleService\Regens\StaffRegen;

class GenerateManager
{
    public function __construct(int $rank)
    {
        $this->rank   = $rank;
        $this->manager = new \stdClass();
    }

    public function makeManager()
    {
        return $this->manager;
    }

    public function setPotentialBasedOnRank()
    {

    }
}
