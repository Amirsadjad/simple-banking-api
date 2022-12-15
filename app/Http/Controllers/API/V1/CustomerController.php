<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\TransactionActionEnum;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'customers' => Customer::all()
            ]
        ]);
    }

    /**
     * @param Customer $customer
     * @return JsonResponse
     */
    public function show(Customer $customer): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => compact('customer')
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|max:255',
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'status' => 'fail',
                'data' => $validationException->errors()
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'customer' => Customer::create($request->only('name'))
            ]
        ]);
    }
}
