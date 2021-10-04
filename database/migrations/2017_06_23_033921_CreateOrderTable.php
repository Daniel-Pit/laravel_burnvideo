<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('orders', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('ordertag', 255);
			$table->integer('uid');
			$table->integer('devicetype')->default(0);
			$table->string('zipurl')->nullable();
			$table->string('weight');
			$table->string('status');
			$table->integer('filecount')->default(0);
			$table->integer('dvdcount')->default(0);
			$table->string('dvdtitle')->nullable();
			$table->string('dvdcaption')->nullable();
            $table->timestamps();
			$table->unsignedTinyInteger('burn_lock', 1)->default(0);
			$table->unsignedTinyInteger('burn_app', 1)->default(0);
			$table->unsignedTinyInteger('burn_app_num', 1)->default(0);
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
		Schema::drop('orders');
    }
}
