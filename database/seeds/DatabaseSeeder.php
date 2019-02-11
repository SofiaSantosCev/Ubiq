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
    	DB::table('roles')->insert([
    		'name'=>'admin'
    	]);

        DB::table('roles')->insert([
            'name'=>'final'
        ]);

        

    }
}
