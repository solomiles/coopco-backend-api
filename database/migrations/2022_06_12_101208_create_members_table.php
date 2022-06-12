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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->char('firstname', 255);
            $table->char('lastname', 255);
            $table->char('othernames', 255);
            $table->char('email', 255)->unique();
            $table->char('password', 255);
            $table->char('gender', 50);
            $table->foreignId('cooperative_id')->constrained('cooperative');
            $table->char('phone', 50);
            $table->char('photo', 255)->nullable();
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
        Schema::dropIfExists('members');
    }
};
