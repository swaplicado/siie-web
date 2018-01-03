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
    }

    public function importPartners()
    {
      $partner = new SImportPartners();
      $partner->importPartners();
    }
    public function importBranches()
    {
      $branch = new SImportBranches();
      $branch->importBranches();
    }
    public function importAddresses()
    {
      $address = new SImportAddresses();
      $address->importAddresses();
    }
    public function importDocuments($iYear = '2017')
    {
      $documents = new SImportDocuments('erp_universal');
      $documents->importDocuments($iYear);
    }
    public function importDocumentRows($iYear = '2017')
    {
      $rows = new SImportDocumentRows('erp_universal');
      $rows->importRows($iYear, '<');
    }
    public function importDocumentRowsLast($iYear = '2017')
    {
      $rows = new SImportDocumentRows('erp_universal');
      $rows->importRows($iYear, '>');
    }

    public function importationDocuments(Request $request)
    {
       $bItems = $request->input('items');
       $bPartners = $request->input('partners');
       $bBranches = $request->input('branches');
       $bAddresses = $request->input('addresses');
       $iYear = $request->input('year');
       $bDocs = $request->input('docs');
       $bRows1 = $request->input('rows1');
       $bRows2 = $request->input('rows2');

       if (! is_null($bItems)) {
          $this->importItems();
       }
       if (! is_null($bPartners)) {
          $this->importPartners();
       }
       if (! is_null($bBranches)) {
          $this->importBranches();
       }
       if (! is_null($bAddresses)) {
          $this->importAddresses();
       }
       if (! is_null($bDocs)) {
          $this->importDocuments($iYear);
       }
       if (! is_null($bRows1)) {
          $this->importDocumentRows($iYear);
       }
       if (! is_null($bRows2)) {
          $this->importDocumentRowsLast($iYear);
       }

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
