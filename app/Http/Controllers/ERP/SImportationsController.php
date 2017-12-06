<?php namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SProcess;

use App\SImportations\SImportFamilies;
use App\SImportations\SImportGroups;
use App\SImportations\SImportGenders;
use App\SImportations\SImportUnits;
use App\SImportations\SImportItems;
use App\SImportations\SImportPartners;
use App\SImportations\SImportBranches;
use App\SImportations\SImportAddresses;

class SImportationsController extends Controller {

    private $oCurrentUserPermission;
    private $iFilter;
    private $iFilterBp;
    private $sClassNav;

    public function __construct()
    {
        $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.CENTRAL_CONFIG'), \Config::get('scsys.MODULES.ERP'));

        $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
        $this->iFilterBp = \Config::get('scsiie.ATT.ALL');
    }

    public function importFromSiie()
    {
      //  $go = new SImportFamilies();
      //  $go->importFamilies();
      //  $group = new SImportGroups();
      //  $group->importGroups();
      //  $gender = new SImportGenders();
      //  $gender->importGenders();
      //  $unit = new SImportUnits();
      //  $unit->importUnits();
      //  $item = new SImportItems();
      //  $item->importItems();
      //  $partner = new SImportPartners();
      //  $partner->importPartners();
      //  $branch = new SImportBranches();
      //  $branch->importBranches();
       $address = new SImportAddresses();
       $address->importAddresses();

       return redirect()->route('siie.home', 1);
    }

}
