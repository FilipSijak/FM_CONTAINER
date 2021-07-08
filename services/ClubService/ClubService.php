<?php

namespace Services\ClubService;

use Services\ClubService\SquadAnalysis\SquadAnalysis;

class ClubService
{
    public function clubInternalSquadAnalysis(array $squadList)
    {
        $squadAnalysis = new SquadAnalysis($squadList);

        $squadAnalysis->squadAnalysisResult();
        /*
         * Daily
         *
         * Monthly (check 1st day of every month)
         * - check for major weaknesses (some positions may have less talented players)
         * - check for squad depth
         * - set a shortlist of positions needed
         * - shortlist would need to have a priority status, player importance flag (key player, backup player),
         * chance of getting the player in
         */
    }

    public function incomingTransferRequest()
    {
        /*
         * check for player importance
         * - based on importance, set the price or reject the transfer completely
         * - if there is a good player on the shortlist, make club more willing to sell
         */
    }

    public function incomingLoanRequest()
    {

    }



    public function clubInternalStaffAnalysis()
    {

    }
}