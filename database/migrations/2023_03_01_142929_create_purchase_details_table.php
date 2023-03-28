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
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->string('order');
            $table->unsignedBigInteger('purchase_id');
            $table->string('name');
            $table->text('description');
            $table->integer('qty');
            $table->double('price');
            $table->double('total');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('purchase_id')->references('id')->on('purchase_orders')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_details');
    }
};
