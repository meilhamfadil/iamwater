<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConditionArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('condition_area', function (Blueprint $table) {
            $table->id();
            $table->integer('condition_id');
            $table->float('x_start');
            $table->float('y_start');
            $table->float('x_end');
            $table->float('y_end');
            $table->float('area');
            $table->float('momen');
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
        Schema::dropIfExists('condition_area');
    }
}
