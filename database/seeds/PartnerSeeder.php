<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Config::set('database.connections.siie.database', session()->has('company') ? session('company')->database_name : "siie_cartro");
        factory(App\ERP\SPartner::class, 50)->create();
    }
}
