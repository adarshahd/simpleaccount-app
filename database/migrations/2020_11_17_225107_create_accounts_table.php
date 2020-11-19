<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('bank_branch')->nullable();
            $table->string('account_name');
            $table->string('account_number');
            $table->enum('account_type', ['savings', 'current', 'fixed_deposit', 'recurring_deposit']);
            $table->string('ifsc')->nullable();
            $table->double('balance')->default(0);
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('accounts');
    }
}
