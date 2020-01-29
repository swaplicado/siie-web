<?php namespace App\QMS\core;

use Illuminate\Http\Request;
use App\Http\Controllers\WMS\SStockController;
use Carbon\Carbon;
use App\MMS\SProductionOrder;

use App\QMS\SQDocConfiguration;
use App\QMS\SQDocSection;
use App\QMS\SElementField;
use App\WMS\SWmsLot;

/**
 *
 */
class SQDocsCore {

    public static function getConfigurations(SProductionOrder $father = null, SProductionOrder $son = null, int $cfgZone = 0)
    {
        $itemFather = SQDocsCore::getItemData($father->item_id);
        
        if ($son != null) {
            $itemSon = SQDocsCore::getItemData($son->item_id);

            $item = $itemSon->id_item;
            $gender = $itemSon->id_item_gender;
            $group = $itemSon->item_group_id;
            $family = $itemSon->item_family_id;
            $class = $itemSon->item_class_id;
            $type = $itemSon->item_type_id;
        }
        else {
            $itemSon = null;
        }

        $itemF = $itemFather->id_item;
        $genderF = $itemFather->id_item_gender;
        $groupF = $itemFather->item_group_id;
        $familyF = $itemFather->item_family_id;
        $classF = $itemFather->item_class_id;
        $typeF = $itemFather->item_type_id;

        $lConfigs = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('qms_doc_configurations as qdc')
                            ->join('qms_doc_elements as qde', 'qdc.element_id', '=', 'qde.id_element')
                            ->join('qmss_element_types as qet', 'qde.element_type_id', '=', 'qet.id_element_type')
                            ->where('qdc.is_deleted', false)
                            ->where('qdc.config_zone_id', $cfgZone)
                            ->where(function ($query) use ($itemFather, $itemSon) {

                                $query->where(function ($query) use ($itemFather) {
                                            $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.CLASS'))
                                            ->where('item_link_id', '=', $itemFather->item_class_id);
                                        })
                                    ->orWhere(function ($query) use ($itemFather) {
                                        $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.TYPE'))
                                            ->where('item_link_id', '=', $itemFather->item_type_id);
                                    })
                                    ->orWhere(function ($query) use ($itemFather) {
                                        $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.FAMILY'))
                                            ->where('item_link_id', '=', $itemFather->item_family_id);
                                    })
                                    ->orWhere(function ($query) use ($itemFather) {
                                        $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.GROUP'))
                                            ->where('item_link_id', '=', $itemFather->item_group_id);
                                    })
                                    ->orWhere(function ($query) use ($itemFather) {
                                        $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.GENDER'))
                                            ->where('item_link_id', '=', $itemFather->id_item_gender);
                                    })
                                    ->orWhere(function ($query) use ($itemFather) {
                                        $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.ITEM'))
                                            ->where('item_link_id', '=', $itemFather->id_item);
                                    })
                                    ->orWhere(function ($query) use ($itemFather) {
                                        $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.ALL'))
                                            ->where('item_link_id', '=', 1);
                                    })
                                    ->orWhere(function ($query) use ($itemSon) {
                                        if ($itemSon != null) {
                                            $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.ITEM'))
                                                ->where('item_link_id', '=', $itemSon->id_item);
                                        }
                                    })
                                    ->orWhere(function ($query) use ($itemSon) {
                                        if ($itemSon != null) {
                                            $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.GENDER'))
                                                ->where('item_link_id', '=', $itemSon->id_item_gender);
                                        }
                                    })
                                    ->orWhere(function ($query) use ($itemSon) {
                                        if ($itemSon != null) {
                                            $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.GROUP'))
                                                ->where('item_link_id', '=', $itemSon->item_group_id);
                                        }
                                    })
                                    ->orWhere(function ($query) use ($itemSon) {
                                        if ($itemSon != null) {
                                            $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.FAMILY'))
                                                ->where('item_link_id', '=', $itemSon->item_family_id);
                                        }
                                    })
                                    ->orWhere(function ($query) use ($itemSon) {
                                        if ($itemSon != null) {
                                            $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.TYPE'))
                                                ->where('item_link_id', '=', $itemSon->item_type_id);
                                        }
                                    })
                                    ->orWhere(function ($query) use ($itemSon) {
                                        if ($itemSon != null) {
                                            $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.CLASS'))
                                                ->where('item_link_id', '=', $itemSon->item_class_id);
                                        }
                                    });
                            })
                            ->select(
                                'qdc.id_configuration', 'qdc.item_link_type_id',
                                'qdc.item_link_id', 'qdc.section_id',
                                'qdc.element_id', 'qde.element', 'qde.n_values',
                                'qde.analysis_id', 'qde.element_type_id', 'qet.element_type',
                                'qet.table_name', 'qet.is_table'
                                );

        $lConfigurations = clone $lConfigs;
        $qSections = clone $lConfigs;

        $aSections = $qSections->select('section_id')
                                ->distinct()
                                ->get();

        $lSections = SQDocSection::whereIn('id_section', json_decode(json_encode($aSections), true))
                                    ->select('id_section', 'title')
                                    ->orderBy('order', 'ASC')
                                    ->get();
                            
        $lConfigurations = $lConfigurations->orderBy('id_configuration', 'ASC')
                                    ->get();

        foreach ($lConfigurations as $config) {
            $config->lFields = SElementField::where('element_id', $config->element_id)
                                            ->where('is_deleted', false)
                                            ->select('id_field', 
                                                    'field_default_value', 
                                                    'field_name', 
                                                    'element_id', 
                                                    'is_reported')
                                            ->get();
        }

        $result = [$lSections, $lConfigurations];

        return $result;
    }

    /**
     * return date based on lot string
     * 
     * example:
     * Lot: K18091902
     * return "2019-09-18"
     *
     * @param int $lot
     * @return Carbon date
     */
    public static function getLotDate(string $lot = "") {
        if (strlen($lot) != 9) {
            return Carbon::createFromDate(2017, 01, 01);
        }

        $year = substr($lot, -4, 2);
        $month = substr($lot, 3, 2);
        $day = substr($lot, 1, 2);

        $carbonDate = Carbon::createFromDate('20'.$year, $month, $day);

        return $carbonDate;
    }

    public static function getItemData(int $itemId = 0)
    {
        $lItemData = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('erpu_items as ei')
                            ->join('erpu_item_genders as eig', 'ei.item_gender_id', '=', 'eig.id_item_gender')
                            ->join('erpu_item_groups as eigr', 'eig.item_group_id', '=', 'eigr.id_item_group')
                            // ->join('erps_item_types as eit', 'eig.item_type_id', '=', 'eit.id_item_type')
                            // ->join('erps_item_classes as eic', 'eig.item_class_id', '=', 'eic.id_item_class');
                            ->select('id_item',
                                    'id_item_gender',
                                    'item_group_id',
                                    'item_class_id',
                                    'item_family_id',
                                    'item_type_id'
                                    );
                                    
        $lItemData = $lItemData->where('ei.id_item', $itemId)
                                ->first();

        return $lItemData;
    }
}
