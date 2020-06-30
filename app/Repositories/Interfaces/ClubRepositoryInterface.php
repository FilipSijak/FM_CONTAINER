<?php

namespace App\Repositories\Interfaces;

interface ClubRepositoryInterface
{
    public function getAllClubsByGame(int $gameId);
}