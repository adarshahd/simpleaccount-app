<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('debits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bill_id');
            $table->string('bill_number', 100);
            $table->date('bill_date');
            $table->float('sub_total');
            $table->float('discount')->default(0.0);
            $table->float('tax');
            $table->float('total');
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('debits');
    }
}
