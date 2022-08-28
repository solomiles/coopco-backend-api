<?php

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
        Schema::create('balance_records', function (Blueprint $table) {
            $table->id();
            $table->double('debit_amount')->default(0);
            $table->double('credit_amount')->default(0);
            $table->double('balance')->default(0);
            $table->integer('month');
            $table->integer('year');
            $table->foreignId('balance_id')->constrained('balances')->onDelete('cascade');
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
        Schema::dropIfExists('balance_records');
    }
};
