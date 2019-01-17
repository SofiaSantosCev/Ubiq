<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    	DB::('role')->insert([
    		'name'=>'admin'
    		'created_at' => Carbon::
    	]);

        // $this->call(UsersTableSeeder::class);
        DB::('user')->insert([
        	'name'=>'Sofia',



        ]);

    }
}
