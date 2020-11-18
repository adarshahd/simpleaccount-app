<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bill_id');
            $table->string('bill_number', 100);
            $table->date('bill_date');
            $table->float('total');
            $table->string('payment_method', 100)->nullable();
            $table->string('payment_reference', 200)->nullable();
            $table->mediumText('notes')->nullable();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('receipts');
    }
}
