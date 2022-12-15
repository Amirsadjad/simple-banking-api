<?php

namespace App\Enums;

enum TransactionActionEnum: string
{
    case Deposit = 'depost';
    case Withdraw = 'withdraw';
    case Transfer = 'transfer';
}
