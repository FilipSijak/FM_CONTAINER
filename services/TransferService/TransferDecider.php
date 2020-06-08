<?php

namespace Services\TransferService;

use App\Repositories\Interfaces\ClubRepositoryInterface;

class TransferDecider
{
    /**
     * @var ClubRepositoryInterface
     */
    protected $clubRepository;

    public function __construct(ClubRepositoryInterface $clubRepository)
    {
        $this->clubRepository = $clubRepository;
    }

    private function playersNeeded()
    {

    }

    private function luxuryDeals()
    {

    }
}
