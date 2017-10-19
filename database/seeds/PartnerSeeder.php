<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\SUtils\SConnectionUtils;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SConnectionUtils::reconnectCompany('siie_cartro');
        factory(App\ERP\SPartner::class, 50)->create();
    }
}
