<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    /**
     * constructor
     */
    public function __construct()
    {
        $this->middleware('auth:api')->only('register');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        if (! $request->user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        try {
            $this->validate($request, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'password' => ['required', Password::min(10)->mixedCase()->numbers()->symbols()],
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
                'message' => 'User created successfully',
                'user' => User::create(
                    $request->only(['name', 'email']) +
                    ['password' => bcrypt($request->password)]
                ),
            ]
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = $this->validate($request, [
                'email' => 'required|email|exists:users,email',
                'password' => 'required',
            ]);
        } catch (ValidationException $ve) {
            return response()->json([
                'status' => 'fail',
                'data' => $ve->errors()
            ]);
        }

        if (! $token = Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'auth' => [
                'type' => 'bearer',
                'token' => $token
            ]
        ]);
    }
}
