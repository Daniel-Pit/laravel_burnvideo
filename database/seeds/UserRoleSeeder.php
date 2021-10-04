<?php

use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        //Sentry::createGroup(array(
        //    'name' => 'Admin'
        //));
        //Sentry::createGroup(array(
        //    'name' => 'User'
        //));

		Sentinel::getRoleRepository()->createModel()->create([
			'name' => 'Admin',
			'slug' => 'admin',
		]);

		Sentinel::getRoleRepository()->createModel()->create([
			'name' => 'User',
			'slug' => 'user',
		]);
    }
}
