<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class reSyncController extends Controller
{
    public function reOrder()
    {
        $erp = collect(\DB::connection('erp_mysql')->table('bpsu_bpb_add')->select('id_bpb', 'id_add')->get());
        $arr_erp = \DB::connection('erp_mysql')->table('bpsu_bpb_add')->select('id_bpb')->get();
        $collection_erp = collect($arr_erp);
        $unique_erp = $collection_erp->pluck('id_bpb');
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        foreach($unique_erp as $id){
            $out->write($id.' - ');
            $external = collect(\DB::connection('siie_sap_mysql')->table('erpu_branch_addresses')->where('external_id', $id)->select('id_branch_address', 'external_id', 'external_ad_id')->get());
            if(sizeof($external) > 1){
                $erp_add = $erp->where('id_bpb', $id)->sortBy('id_add');
                if(sizeof($external) > sizeof($erp_add)){
                    foreach($erp_add as $elem){
                        $external->shift();
                    }
                    foreach($external as $val){
                        \DB::connection('siie_sap_mysql')->table('erpu_branch_addresses')->where('id_branch_address', $val->id_branch_address)->delete();
                    }
                    $external = \DB::connection('siie_sap_mysql')->table('erpu_branch_addresses')->where('external_id', $id)->select('id_branch_address', 'external_id', 'external_ad_id')->get();
                    foreach($external as $item){
                        $item->external_ad_id = $erp_add->first()->id_add;
                        $erp_add = $erp_add->filter(function ($value) use($item) {
                                            return $value->id_add != $item->external_ad_id;
                                    });
                        // $erp_add = $erp_add->whereNotIn('id_add', $item->external_ad_id);
                    }
                    foreach($external as $item){
                        \DB::connection('siie_sap_mysql')->table('erpu_branch_addresses')->where('id_branch_address', $item->id_branch_address)->update(['external_ad_id' => $item->external_ad_id]);
                    }
                }else{
                    foreach($external as $item){
                        $item->external_ad_id = $erp_add->first()->id_add;
                        $erp_add = $erp_add->filter(function ($value) use($item) {
                                        return $value->id_add != $item->external_ad_id;
                                   });
                        // $erp_add = $erp_add->whereNotIn('id_add', $item->external_ad_id);
                    }
                    foreach($external as $item){
                        \DB::connection('siie_sap_mysql')->table('erpu_branch_addresses')->where('id_branch_address', $item->id_branch_address)->update(['external_ad_id' => $item->external_ad_id]);
                    }
                }
            }
        }
        $out->writeln("Fin reOrder");
        dd("fin reOrder");
    }

    public function reSync(){
        $locu_sta = collect(\DB::connection('erp_mysql')->table('locu_sta')->select('id_sta','sta_code', 'fid_cty')->get());
        $country_states =  collect(\DB::connection('siie_sap_mysql')->table('erps_country_states')->get());
        $countries =  collect(\DB::connection('siie_sap_mysql')->table('erps_countries')->get());
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        $erp = collect(\DB::connection('erp_mysql')->table('bpsu_bpb_add')->get());
        foreach ($erp as $item) {
            $exist = \DB::connection('siie_sap_mysql')->table('erpu_branch_addresses')
                        ->where('external_id', $item->id_bpb)
                        ->where('external_ad_id', $item->id_add)
                        ->first();
            $out->write('['.$item->id_bpb.' - '.$item->id_add.'] ');
            $state = $locu_sta->where('id_sta', $item->fid_sta_n)->first();
            $state_id =  $country_states->where('code', is_null($state) ? 0 : $state->sta_code)->first();
            $country_id =  $countries->where('code', is_null($state) ? '' : strval($state->fid_cty))->first();
            if(!is_null($exist)){
                \DB::connection('siie_sap_mysql')->table('erpu_branch_addresses')
                    ->where('external_id', $item->id_bpb)
                    ->where('external_ad_id', $item->id_add)
                    ->update([
                        'name' => $item->bpb_add, 'street' => $item->street, 'num_ext' => $item->street_num_ext,
                        'num_int' => $item->street_num_int, 'neighborhood' => $item->neighborhood,
                        'reference' => $item->reference, 'locality' => $item->locality, 'county' => $item->county,
                        'state_name' => $item->state, 'zip_code' => $item->zip_code, 'is_main' => $item->b_def,
                        'is_deleted' => $item->b_del, 'country_id' => is_null($country_id) ? 1 : $country_id->id_country,
                        'state_id' => is_null($state_id) ? 1 : $state_id->id_state,
                    ]);
            }else{
                $branch_id = \DB::connection('siie_sap_mysql')->table('erpu_branch_addresses')->where('external_id', $item->id_bpb)->value('branch_id');
                \DB::connection('siie_sap_mysql')->table('erpu_branch_addresses')->insert([
                    ['name' => $item->bpb_add, 'street' => $item->street, 'num_ext' => $item->street_num_ext,
                    'num_int' => $item->street_num_int, 'neighborhood' => $item->neighborhood,
                    'reference' => $item->reference, 'locality' => $item->locality, 'county' => $item->county,
                    'state_name' => $item->state, 'zip_code' => $item->zip_code, 'external_id' => $item->id_bpb,
                    'external_ad_id' => $item->id_add, 'is_main' => $item->b_def, 'is_deleted' => $item->b_del,
                    'branch_id' => $branch_id, 'country_id' => is_null($country_id) ? 1 : $country_id->id_country,
                    'state_id' => is_null($state_id) ? 1 : $state_id->id_state,
                    'created_by_id' => 1, 'updated_by_id' => 1, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d')]
                ]);
            }

        }
        $out->writeln("Fin reSync");
        dd("fin reSync");
    }

    public function reMap(){
        $erp_sc = collect(\DB::connection('erp_sc_mysql')->table('trn_dps')->where('id_year', '>=', 2021)->get());
        $siie_sap = collect(\DB::connection('siie_sap_mysql')->table('erpu_branch_addresses')->get());
        $siie_sap_doc = collect(\DB::connection('siie_sap_mysql')->table('erpu_documents')->where('dt_date', '>', '2021-01-01')->get());
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        foreach($siie_sap_doc as $sap_doc){
            $ids = explode('_', $sap_doc->external_id, 2);
            $out->write('['.$ids[0].' - '.$ids[1].'] ');
            $trn_doc = $erp_sc->where('id_year', (Integer)$ids[0])->where('id_doc', (Integer)$ids[1])->first();
            $fid_bpb = $trn_doc->fid_bpb;
            $fid_add = $trn_doc->fid_add;
            $address = $siie_sap->where('external_id', $fid_bpb)->where('external_ad_id', $fid_add)->first();
            \DB::connection('siie_sap_mysql')->table('erpu_documents')->where('id_document', $sap_doc->id_document)->update(['address_id' => $address->id_branch_address]);
        }
        $out->writeln("Fin reMap");
        dd("fin reMap");

    }
}
