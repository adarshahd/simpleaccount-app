<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->mediumText('identification');
            $table->bigInteger('stock');
            $table->bigInteger('total_stock');
            $table->float('mrp');
            $table->float('price');
            $table->float('tax');
            $table->string('hsn')->default("");
            $table->string('batch');
            $table->date('expiry')->nullable();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('manufacturer_id')->constrained()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_stocks');
    }
}
