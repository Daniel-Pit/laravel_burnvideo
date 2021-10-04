<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  
    public function up()
    {
        //
		Schema::create('file', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('uid');
			$table->string('ftype');
			$table->string('furl');
			$table->string('ftsurl');
			$table->string('fzipurl');
			$table->integer('fplaytime');
			$table->integer('fweight');
			$table->integer('fstatus')->default(0);
			$table->integer('finserttime');
			$table->timestamps();
			$table->integer('file_index')->default(0);
			$table->string('ct_caption', 1024)->nullable();
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
		Schema::drop('file');
    }
}
