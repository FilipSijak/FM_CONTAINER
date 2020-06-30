<?php

namespace Services\TransferService;

use App\Repositories\Interfaces\ClubRepositoryInterface;
use Services\TransferService\Interfaces\TransferServiceInterface;

class TransferService implements TransferServiceInterface
{
    /**
     * @var int
     */
    protected $gameId;

    /**
     * @var ClubRepositoryInterface
     */
    protected $clubRepository;

    public function __construct(int $gameId, ClubRepositoryInterface $clubRepository)
    {
        $this->gameId = $gameId;
        $this->clubRepository = $clubRepository;
    }

    public function allClubsPotentialBids()
    {
        $potentialBids = [];

        foreach ($this->clubRepository->getAllClubsByGame(1) as $club) {
            $potentialBids[] = '';
        }
    }

    public function processTransferBids()
    {
        // TODO: Implement processTransferBids() method.
    }
}
