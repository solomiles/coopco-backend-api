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
        Schema::create('loan_records', function (Blueprint $table) {
            $table->id();
            $table->double('debit_amount')->default(0);
            $table->double('credit_amount')->default(0);
            $table->double('balance')->default(0);
            $table->double('interest')->default(0);
            $table->double('accumulated_interest')->default(0);
            $table->integer('month');
            $table->integer('year');
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
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
        Schema::dropIfExists('loan_records');
    }
};
