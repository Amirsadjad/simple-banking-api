<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountTransactionController extends Controller
{
    /**
     * @param BankAccount $account
     * @return JsonResponse
     */
    public function index(BankAccount $account): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'account' => $account,
                'transactions' => $account->transactions()->get()
            ]
        ]);
    }

    /**
     * @param BankAccount $account
     * @param Transaction $transaction
     * @return JsonResponse
     */
    public function show(BankAccount $account, Transaction $transaction): JsonResponse
    {
        if (! in_array($account->id, $transaction->only(['sender_account', 'receiver_account']))) {
            return response()->json([
                'status' => 'fail',
                'data' => [
                    'account' => 'account and transaction don\'t match'
                ]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => compact( 'account', 'transaction')
        ]);
    }
}
