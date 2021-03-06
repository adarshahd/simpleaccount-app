<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebitItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('debit_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('quantity');
            $table->float('price', 10, 5);
            $table->float('tax_percent');
            $table->float('tax', 10, 5);
            $table->float('discount')->default(0.0);
            $table->float('sub_total', 10, 5);
            $table->float('total', 10, 5);
            $table->string('purchase_id')->nullable();
            $table->foreignId('debit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_stock_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('debit_items');
    }
}
