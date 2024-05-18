<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->integer('parent')->default(0);
            $table->text('role_ids')->nullable();
            $table->string('name');
            $table->enum('type', ['label', 'menu'])->default('menu');
            $table->string('icon')->nullable(true);
            $table->string('link')->nullable(true);
            $table->integer('order')->default(1);
            $table->enum('target', ['_self', '_blank'])->default('_self');
            $table->boolean('manageable')->default(true);
            $table->softDeletes('deleted_at', 0);
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
        Schema::dropIfExists('menu');
    }
}
