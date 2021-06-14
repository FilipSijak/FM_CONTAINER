<?php

namespace Services\TransferService;

use App\Repositories\Interfaces\ClubRepositoryInterface;
use Services\TransferService\Interfaces\TransferServiceInterface;
use Services\TransferService\TransferRequest\TransferRequestAnalysis;
use Services\TransferService\TransferRequest\TransferRequestValidator;

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
        $this->gameId         = $gameId;
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

    public function makeTransferRequest(array $requestParams)
    {
        $transferAnalysis = new TransferRequestAnalysis();
        $transferProcess = new TransferProcess($transferAnalysis);
        $transferFactory = new TransferFactory();
        $transferRequestValidator = new TransferRequestValidator();

        switch ($requestParams['transfer_type']) {
            case TransferTypes::FREE_TRANSFER:
                $validationFields = $transferRequestValidator->validateFreeTransferRequest($requestParams);

                if (!empty($validationFields)) {
                    return false;
                }

                break;
            case TransferTypes::LOAN_TRANSFER:
                $validationFields = $transferRequestValidator->validateLoanTransferRequest($requestParams);

                if (!empty($validationFields)) {
                    return false;
                }

                break;
            case TransferTypes::PERMANENT_TRANSFER:

                $validationFields = $transferRequestValidator->validatePermanentTransferRequest($requestParams);

                if (!empty($validationFields)) {
                    return false;
                }

                $approval = $transferProcess->getTransferApproval($requestParams);

                if ($approval) {
                    $transfer = $transferFactory->createTransfer($requestParams);
                    $transfer->save();
                }

                break;
        }
    }

    public function transferRequestResponse(array $requestParams)
    {
        switch ($requestParams['type']) {
            case TransferTypes::FREE_TRANSFER:
                // player decision

                break;
            case TransferTypes::LOAN_TRANSFER:
                // club analysis (availability)
                // player decision

                break;
            case TransferTypes::PERMANENT_TRANSFER:
                // club analysis (valuation, availability, budget, etc.)
                // player decision

                break;
        }
    }
}
