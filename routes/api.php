<?php

use App\Http\Controllers\API\V1\AccountStateController;
use App\Http\Controllers\API\V1\AccountTransactionController;
use App\Http\Controllers\API\V1\AuthenticationController;
use App\Http\Controllers\API\V1\CustomerAccountController;
use App\Http\Controllers\API\V1\CustomerController;
use App\Http\Controllers\API\V1\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::name('api.')->group(function () {

    Route::prefix('v1')->name('v1.')->group(function () {

        Route::prefix('auth')->name('auth.')
            ->controller(AuthenticationController::class)->group(function () {
                Route::post('register', 'register')->name('register');
                Route::post('login', 'login')->name('login');
            });

        Route::middleware('auth:api')->group(function () {

            Route::resource('customers', CustomerController::class)->only(['index', 'show', 'store']);
            Route::resource('customers.accounts', CustomerController::class)->only(['index', 'show', 'store']);
            Route::resource('accounts.transactions', AccountTransactionController::class)->only(['index', 'show']);
            Route::resource('accounts.states', AccountStateController::class)->only(['index', 'show']);

            Route::prefix('transactions')->name('transactions.')
                ->controller(TransactionController::class)->group(function () {
                    Route::get('{transaction}/show', 'show')->name('show');
                    Route::post('deposit', 'deposit')->name('deposit');
                    Route::post('withdrawal', 'withdrawal')->name('withdrawal');
                    Route::post('transfer', 'transfer')->name('transfer');
                });

        });

    });

});
