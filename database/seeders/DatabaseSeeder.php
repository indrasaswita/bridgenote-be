<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
		for($i = 0; $i < 1000; $i++) {
			DB::table('users')->insert([
				'name' => Str::random(10),
				'email' => Str::random(10).'@gmail.com',
				'password' => Hash::make('password'),
				'status' => round(rand(0,1)) == 0 ? 'Inactive' : 'Active',
				'position' => "Level " . round(rand(1,5)),
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now(),
			]);
		}
	}
}
