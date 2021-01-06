<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bill_id');
            $table->string('bill_number', 100);
            $table->date('bill_date');
            $table->float('sub_total', 10, 5);
            $table->float('discount')->default(0.0);
            $table->float('tax', 10, 5);
            $table->float('total', 10, 5);
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable();
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
        Schema::dropIfExists('sales');
    }
}
