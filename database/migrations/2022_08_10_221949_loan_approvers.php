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
        Schema::create('loan_approvers', function (Blueprint $table) {
            $table->id();
            $table->string('approver_name')->nullable();
            $table->string('approver_type');
            $table->foreignId('loan_application_id')->constrained('loan_applications')->onDelete('cascade');
            $table->integer('approver_id')->nullable();
            $table->enum('status', ['pending', 'approved', 'denied'])->default('pending');
            $table->text('action_reason')->nullable();
            $table->integer('from_approver')->nullable();
            $table->timestamp('date_of_action');
            $table->softDeletes();
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
        Schema::dropIfExists('loan_approvers');
    }
};
