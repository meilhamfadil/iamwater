<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConditionProcess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('condition_process', function (Blueprint $table) {
            $table->id();
            $table->integer('condition_id');
            $table->float('ph_asam');
            $table->float('ph_baik');
            $table->float('ph_basa');
            $table->float('metal_baik');
            $table->float('metal_sedang');
            $table->float('metal_buruk');
            $table->float('oxygen_baik');
            $table->float('oxygen_cukup');
            $table->float('oxygen_buruk');
            $table->float('tds_baik');
            $table->float('tds_sedang');
            $table->float('tds_buruk');
            $table->float('category_sangat_buruk');
            $table->float('category_buruk');
            $table->float('category_sedang');
            $table->float('category_baik');
            $table->float('category_sangat_baik');
            $table->float('area');
            $table->float('momen');
            $table->float('output');
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
        Schema::dropIfExists('condition_process');
    }
}
