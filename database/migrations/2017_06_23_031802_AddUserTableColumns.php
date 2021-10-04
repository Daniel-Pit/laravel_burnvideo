<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::table('users', function(Blueprint $table)
		{
			$table->string('street')->after('password')->nullable();
			$table->string('city')->after('street')->nullable();
			$table->string('state')->after('city')->nullable();
			$table->string('zipcode')->after('state')->nullable();
			$table->string('apncode')->after('zipcode')->nullable();
			$table->string('gcmcode')->after('apncode')->nullable();
			$table->string('mon_weight')->after('gcmcode')->nullable();
			$table->unsignedInteger('mon_nextday')->after('mon_weight')->nullable();
			$table->unsignedInteger('mon_freedvd')->after('mon_nextday')->default(0);
			$table->unsignedInteger('first_ordertime')->after('mon_freedvd')->default(0);
			$table->string('token')->after('first_ordertime')->nullable();
			$table->string('customer_id')->after('token')->nullable();
			$table->unsignedTinyInteger('first_ordermail')->after('updated_at')->default(0);
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
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('street');
			$table->dropColumn('city');
			$table->dropColumn('state');
			$table->dropColumn('zipcode');
			$table->dropColumn('apncode');
			$table->dropColumn('gcmcode');
			$table->dropColumn('mon_weight');
			$table->dropColumn('mon_nextday');
			$table->dropColumn('mon_freedvd');
			$table->dropColumn('first_ordertime');
			$table->dropColumn('token');
			$table->dropColumn('customer_id');
			$table->dropColumn('first_ordermail');
		});
    }
}
