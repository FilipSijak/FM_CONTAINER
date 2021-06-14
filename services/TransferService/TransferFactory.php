<?php

namespace Services\TransferService;

use App\Transfers\Transfer;

class TransferFactory
{
    public function createTransfer(array $transferParams)
    {
        $transfer = new Transfer();

        $table = $transfer->getTable();
        $columns = \Schema::getColumnListing($table);

        foreach ($columns as $field) {
            if (isset($transferParams[$field])) {
                $transfer->{$field} = $transferParams[$field];
            }
        }

        return $transfer;
    }
}
