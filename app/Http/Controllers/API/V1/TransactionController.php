<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\TransactionActionEnum;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * @param Transaction $transaction
     * @return JsonResponse
     */
    public function show(Transaction $transaction): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'transaction' => $transaction,
                'accounts_state' => $transaction->accountsState()->get()
            ]
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deposit(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'amount' => 'required|regex:/^[0-9]+\.[0-9]+$/|max:255',
                'account_number' => 'required|exists:bank_accounts,id'
            ], [
                'amount.regex' => 'amount needs to be a float number (e.g. 0.5, 5.0, 5.5)'
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'status' => 'fail',
                'data' => $validationException->errors()
            ]);
        }

        $account = BankAccount::find($request->account_number);

        try {
            DB::beginTransaction();

            $account->update([
                'balance' => $account->balance + $request->amount
            ]);
            $transaction = auth()->user()->makeTransaction([
                'receiver_account' => $account->id,
                'action' => TransactionActionEnum::Deposit,
                'amount' => $request->amount
            ]);
            $account->logState($transaction->id);

            DB::commit();
        } catch (Exception $ignored) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'error' => 'Something went wrong. please rty again.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => compact('account', 'transaction')
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function withdraw(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'amount' => 'required|regex:/^[0-9]+\.[0-9]+$/|max:255',
                'account_number' => 'required|exists:bank_accounts,id'
            ], [
                'amount.regex' => 'amount needs to be a float number (e.g. 0.5, 5.0, 5.5)'
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'status' => 'fail',
                'data' => $validationException->errors()
            ]);
        }

        $account = BankAccount::find($request->account_number);

        if ($account->balance < $request->amount) {
            return response()->json([
                'status' => 'fail',
                'data' => [
                    'account_balance' => 'account\'s balance is not sufficient for this transaction'
                ]
            ]);
        }

        try {
            DB::beginTransaction();

            $account->update([
                'balance' => $account->balance - $request->amount
            ]);
            $transaction = auth()->user()->makeTransaction([
                'sender_account' => $account->id,
                'action' => TransactionActionEnum::Withdrawal,
                'amount' => $request->amount
            ]);
            $account->logState($transaction->id);

            DB::commit();
        } catch (Exception $ignored) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'error' => 'Something went wrong. please rty again.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => compact('account', 'transaction')
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function transfer(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'amount' => 'required|regex:/^[0-9]+\.[0-9]+$/|max:255',
                'sender_account_number' => 'required|exists:bank_accounts,id',
                'receiver_account_number' => 'required|exists:bank_accounts,id'
            ], [
                'amount.regex' => 'amount needs to be a float number (e.g. 0.5, 5.0, 5.5)'
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'status' => 'fail',
                'data' => $validationException->errors()
            ]);
        }

        $senderAccount = BankAccount::find($request->sender_account_number);
        $receiverAccount = BankAccount::find($request->receiver_account_number);

        if ($senderAccount->balance < $request->amount) {
            return response()->json([
                'status' => 'fail',
                'data' => [
                    'account_balance' => 'sender account\'s balance is not sufficient for this transaction'
                ]
            ]);
        }

        try {
            DB::beginTransaction();

            $senderAccount->update([
                'balance' => $senderAccount->balance - $request->amount
            ]);
            $receiverAccount->update([
                'balance' => $receiverAccount->balance + $request->amount
            ]);

            $transaction = auth()->user()->makeTransaction([
                'sender_account' => $senderAccount->id,
                'receiver_account' => $receiverAccount->id,
                'action' => TransactionActionEnum::Transfer,
                'amount' => $request->amount
            ]);

            $senderAccount->logState($transaction->id);
            $receiverAccount->logState($transaction->id);

            DB::commit();
        } catch (Exception $ignored) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'error' => 'Something went wrong. please rty again.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => compact('senderAccount', 'receiverAccount', 'transaction')
        ]);
    }
}
