<?php

namespace Services\MatchService\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface MatchServiceInterface
{
    public function simulateRound(Collection $matches);
}