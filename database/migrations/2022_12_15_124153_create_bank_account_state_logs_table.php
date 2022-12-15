<?php

use App\Models\BankAccount;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_account_state_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('account_number')->references('id')->on('bank_accounts')
                ->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignUlid('transaction_id')->references('id')->on('transactions')
                ->restrictOnDelete()->cascadeOnUpdate();
            $table->string('balance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_account_state_logs');
    }
};
