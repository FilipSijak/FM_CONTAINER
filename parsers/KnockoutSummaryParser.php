<?php

namespace Parsers;

use App\Models\Schema\KnockoutSummary;

class KnockoutSummaryParser
{
    /**
     * @var string
     */
    private $summarySchema;
    /**
     * @var KnockoutSummary
     */
    private $summaryModel;

    public function parseSchema(array $knockoutSummarySchema, KnockoutSummary $knockoutSummaryModel)
    {
        $this->summarySchema = $knockoutSummarySchema;
        $this->summaryModel  = $knockoutSummaryModel;

        $this->summaryModel->setFirstGroup($this->summarySchema["first_group"]);
        $this->summaryModel->setSecondGroup($this->summarySchema["second_group"]);
        /*$this->summaryModel->setFirstPlacedTeam($this->summarySchema["winner"]);
        $this->summaryModel->setSecondPlacedTeam($this->summarySchema["second_placed"]);
        $this->summaryModel->setThirdPlacedTeam($this->summarySchema["third_placed"]);*/
    }
}
