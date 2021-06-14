<?php

namespace Services\TransferService;

use Services\TransferService\TransferRequest\TransferRequestAnalysis;

class TransferProcess
{
    /**
     * @var bool
     */
    private $allowTransfer = true;
    /**#
     * @var TransferRequestAnalysis
     */
    private $transferAnalysis;

    public function __construct(TransferRequestAnalysis $transferAnalysis)
    {
        $this->transferAnalysis = $transferAnalysis;
    }

    public function getTransferApproval(array $params)
    {
        $this->clubApproval($params);
        $this->regulationApproval($params);

        return $this->allowTransfer;
    }

    private function clubApproval(array $params)
    {
        if (!$this->transferAnalysis->evaluateTransfer($params)) {

        }

        $this->allowTransfer = true;

        return $this;
    }

    private function regulationApproval(array $params)
    {
        $this->allowTransfer = true;

        return $this;
    }
}