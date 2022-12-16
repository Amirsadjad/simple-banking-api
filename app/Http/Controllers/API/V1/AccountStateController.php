<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankAccountStateLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountStateController extends Controller
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
                'states' => $account->stateLogs()->get()
            ]
        ]);
    }

    /**
     * @param BankAccount $account
     * @param BankAccountStateLog $state
     * @return JsonResponse
     */
    public function show(BankAccount $account, BankAccountStateLog $state): JsonResponse
    {
        if ($state->account_number !== $account->id) {
            return response()->json([
                'status' => 'fail',
                'data' => [
                    'account' => 'account and state don\'t match'
                ]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => compact( 'account', 'state')
        ]);
    }
}
