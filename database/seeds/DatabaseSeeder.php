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
    		'name'=>'admin',
    	]);

        DB::table('roles')->insert([
            'name'=>'final',
        ]);

        // $this->call(UsersTableSeeder::class);
        DB::table('users')->insert([
        	'name'=>'admin',
            'email' => 'admin@mail.com',
            'password' => password_hash(12345678, PASSWORD_DEFAULT),
            'rol_id' => 1,
            'banned'=>0
        ]);
            




    }
}
