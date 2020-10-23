<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 300);
            $table->string('identification', 200);
            $table->mediumText('address_line_1');
            $table->mediumText('address_line_2')->nullable();
            $table->string('city', 300);
            $table->string('state', 200);
            $table->string('country', 300);
            $table->string('pin', 50);
            $table->string('contact_name', 200);
            $table->string('contact_email', 200)->nullable();
            $table->string('contact_phone', 50);
            $table->string('website', 400)->nullable();
            $table->foreignId('id_type_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('vendors');
    }
}
