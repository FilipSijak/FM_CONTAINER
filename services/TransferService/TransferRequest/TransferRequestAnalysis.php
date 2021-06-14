<?php

namespace Services\TransferService\TransferRequest;

class TransferRequestAnalysis
{
    public function __construct()
    {
    }

    public function evaluateTransfer(array $params)
    {
        return true;
    }

    private function playerQualityEvaluation(int $playerId)
    {
        // scouting report
    }

    private function playerValueEvaluation(int $playerId, int $clubId)
    {
        // how much is club willing/able to pay for the player
    }

    private function clubRequirementsEvaluation(int $clubId)
    {
        // club analysis
    }
}