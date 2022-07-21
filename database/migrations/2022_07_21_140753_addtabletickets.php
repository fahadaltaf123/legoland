<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Addtabletickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('is_universal')->nullable(true);
            $table->integer('no_of_adults')->nullable(true);
            $table->integer('no_of_kids')->nullable(true);
            $table->integer('status')->nullable(true);
            $table->integer('customer_id')->unsigned()->nullable(true);
            $table->dateTime('visit_date')->nullable(true);
            $table->string('visit_time')->nullable(true);
            $table->float('total')->nullable(true);
            $table->float('discount')->nullable(true);
            $table->float('after_discount')->nullable(true);
            $table->float('balance_due')->nullable(true);
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
        //
    }
}
