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
        Schema::create('cooperative', function (Blueprint $table) {
            $table->id();
            $table->char('name', 255);
            $table->text('description');
            $table->char('logo', 255)->nullable();
            $table->foreignId('country_id')->constrained('countries');
            $table->char('location', 255);
            $table->foreignId('plan_id')->constrained('plans');
            $table->char('type', 255);
            $table->json('customizations');
            $table->boolean('active');
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
        Schema::dropIfExists('cooperatives');
    }
};
