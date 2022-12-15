<?php

use App\Models\BankAccount;
use App\Models\User;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('operator_id')->references('id')->on('users')
                ->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('sender_account')->nullable()
                ->references('id')->on('bank_accounts')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('receiver_account')->nullable()
                ->references('id')->on('bank_accounts')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('action');
            $table->string('amount');
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
        Schema::dropIfExists('transactions');
    }
};
