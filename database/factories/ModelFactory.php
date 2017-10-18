<?php
use App\User;
use App\ERP\SPartner;
use Faker\Generator;
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(User::class, function(Generator $faker){
	$array = [
		'username' => $faker->username,
		'email' => $faker->email,
		'password' => bcrypt('1234'),
		'user_type_id' => '2',
		'is_deleted' => 0,
		'created_by_id' => 1,
		'updated_by_id' => 1
	];
	return $array;
});

$factory->define(SPartner::class, function(Generator $faker){
	$array = [
		'name' => $faker->name,
		'last_name' => $faker->lastName,
		'first_name' => $faker->firstName(null),
		'fiscal_id' => '245646876545',
		'person_id' => '8468416541654',
		'external_id' => 0,
		'is_company' => 0,
		'is_customer' => 1,
		'is_supplier' => 0,
		'is_related_party' => 0,
		'is_deleted' => 0,
		'created_by_id' => 1,
		'updated_by_id' => 1
	];
	return $array;
});
