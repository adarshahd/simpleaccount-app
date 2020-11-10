<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->float('price')->nullable();
            $table->float('tax_percent')->nullable();
            $table->float('tax')->nullable();
            $table->float('discount')->default(0)->nullable();
            $table->float('sub_total')->nullable();
            $table->float('total')->nullable();
            $table->bigInteger('product_id');
            $table->bigInteger('order_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
