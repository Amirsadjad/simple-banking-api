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
            $table->foreignIdFor(User::class, 'operator_id')
                ->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(BankAccount::class, 'sender_account')->nullable()
                ->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(BankAccount::class, 'receiver_account')->nullable()
                ->constrained()->restrictOnDelete()->cascadeOnUpdate();
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
