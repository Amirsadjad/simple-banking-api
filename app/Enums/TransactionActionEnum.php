<?php

namespace App\Enums;

enum TransactionActionEnum: string
{
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
    case Transfer = 'transfer';
}
