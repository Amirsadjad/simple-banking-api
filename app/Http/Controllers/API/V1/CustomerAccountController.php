<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\TransactionActionEnum;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Customer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerAccountController extends Controller
{
    /**
     * @param Customer $customer
     * @return JsonResponse
     */
    public function index(Customer $customer): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'customers' => $customer,
                'accounts' => $customer->accounts()->get()
            ]
        ]);
    }

    /**
     * @param Customer $customer
     * @param BankAccount $account
     * @return JsonResponse
     */
    public function show(Customer $customer, BankAccount $account): JsonResponse
    {
        if ($account->customer_id !== $customer->id) {
            return response()->json([
                'status' => 'fail',
                'data' => [
                    'account' => 'account and customer don\'t match'
                ]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => compact('customer', 'account')
        ]);
    }

    /**
     * @param Customer $customer
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Customer $customer, Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'balance' => 'required|regex:/^[0-9]+\.[0-9]+$/|max:255'
            ], [
                'balance.regex' => 'balance needs to be a float number (e.g. 0.5, 5.0, 5.5)'
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'status' => 'fail',
                'data' => $validationException->errors()
            ]);
        }

        try {
            DB::beginTransaction();

            $account = $customer->addAccount($request->only('balance'));
            $transaction = auth()->user()->makeTransaction([
                'receiver_account' => $account->id,
                'action' => TransactionActionEnum::Deposit,
                'amount' => $request->balance
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
            'data' => compact('customer', 'account')
        ]);
    }
}
