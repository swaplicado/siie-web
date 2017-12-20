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
use App\SImportations\SImportDocuments;
use App\SImportations\SImportDocumentRows;
use App\SImportations\SImportDocumentTaxRows;

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

    public function index($isImported = 0)
    {
       return view('siie.imports.importations')
                              ->with('title', 'Importaciones')
                              ->with('isImported', $isImported);
    }

    public function importItems()
    {
       $go = new SImportFamilies();
       $go->importFamilies();
       $group = new SImportGroups();
       $group->importGroups();
       $gender = new SImportGenders();
       $gender->importGenders();
       $unit = new SImportUnits();
       $unit->importUnits();
       $item = new SImportItems();
       $item->importItems();

       return redirect()->route('siie.importation', 1);
    }

    public function importPartners($value='')
    {
      $partner = new SImportPartners();
      $partner->importPartners();

      return redirect()->route('siie.importation', 1);
    }
    public function importBranches($value='')
    {
      $branch = new SImportBranches();
      $branch->importBranches();

      return redirect()->route('siie.importation', 1);
    }
    public function importAddresses($value='')
    {
      $address = new SImportAddresses();
      $address->importAddresses();

      return redirect()->route('siie.importation', 1);
    }
    public function importDocuments($value='')
    {
      $documents = new SImportDocuments('erp_universal');
      $documents->importDocuments(2017);

      return redirect()->route('siie.importation', 1);
    }
    public function importDocumentRows($value='')
    {
      $rows = new SImportDocumentRows('erp_universal');
      $rows->importRows(2017, '<');

      return redirect()->route('siie.importation', 1);
    }
    public function importDocumentRowsLast($value='')
    {
      $rows = new SImportDocumentRows('erp_universal');
      $rows->importRows(2017, '>');

      return redirect()->route('siie.importation', 1);
    }
    // public function importDocumentTaxRows($value='')
    // {
    //   $taxRows = new SImportDocumentTaxRows('erp_universal');
    //   $taxRows->importRows(2017, '<');
    //
    //   return redirect()->route('siie.importation', 1);
    // }
    // public function importDocumentTaxRowsLast($value='')
    // {
    //
    //
    //   return redirect()->route('siie.importation', 1);
    // }

}
