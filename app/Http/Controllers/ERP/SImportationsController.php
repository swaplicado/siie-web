<?php
namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SProcess;

use App\ERP\SErpConfiguration;
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
use App\SImportations\SImportProductionOrders;
use App\SImportations\SImportFormulas;
use App\SImportations\SImportFormulaRows;
use App\SUtils\SDocumentsUtils;

class SImportationsController extends Controller
{

   private $oCurrentUserPermission;
   private $iFilter;
   private $iFilterBp;
   private $sClassNav;
   private $sHost;

   public function __construct()
   {
      $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.IMPORTATIONS'), \Config::get('scsys.MODULES.ERP'));

      $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
      $this->iFilterBp = \Config::get('scsiie.ATT.ALL');
   }

   public function index($isImported = 0, $items, $partners, $branches, $adds, $docs, $rows1, $rows2)
   {

      $oDbImport = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.DB_IMPORT'));
      $oDbHost = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.DB_HOST'));

      return view('siie.imports.importations')
         ->with('title', 'Importaciones')
         ->with('items', $items)
         ->with('partners', $partners)
         ->with('branches', $branches)
         ->with('adds', $adds)
         ->with('docs', $docs)
         ->with('rows1', $rows1)
         ->with('rows2', $rows2)
         ->with('year', session('work_date')->year)
         ->with('isImported', $isImported)
         ->with('db_import', $oDbImport->val_text)
         ->with('db_host', $oDbHost->val_text);
   }

   public function importItems()
   {
      $importations = 0;

      try {

         $unit = new SImportUnits($this->sHost);
         $importations += $unit->importUnits();
         $familiy = new SImportFamilies($this->sHost);
         $importations += $familiy->importFamilies();
         $group = new SImportGroups($this->sHost);
         $importations += $group->importGroups();
         $gender = new SImportGenders($this->sHost);
         $importations += $gender->importGenders();
         $item = new SImportItems($this->sHost);
         $importations += $item->importItems();

      } catch (\Exception $e) {
         return $importations;
      }

      return $importations;
   }

   public function importPartners()
   {
      $partner = new SImportPartners($this->sHost);
      return $partner->importPartners();
   }
   public function importBranches()
   {
      // $Formulas = new \App\SImportations\SImportFormulas($this->sHost);
      // $Formulas->importFormulas();
      // $FormulaRows = new \App\SImportations\SImportFormulaRows($this->sHost);
      // return $FormulaRows->importFormulaRows();
      $branch = new SImportBranches($this->sHost);
      return $branch->importBranches();
   }

   public function importAddresses()
   {
      $address = new SImportAddresses($this->sHost);
      return $address->importAddresses();
   }

   public function importDocuments($sDbName, $iYear = '2017')
   {
      $documents = new SImportDocuments($this->sHost, $sDbName);
      return $documents->importDocuments($iYear);
   }

   public function importDocumentRows($sDbName, $iYear = '2017')
   {
      $rows = new SImportDocumentRows($this->sHost, $sDbName);
      return $rows->importRows($iYear);
   }

   public function importationDocuments(Request $request)
   {
      $bItems = $request->input('items');
      $bPartners = $request->input('partners');
      $bBranches = $request->input('branches');
      $bAddresses = $request->input('addresses');
      $iYear = $request->input('year');
      $sDbName = $request->input('db_name');
      $sDbHost = is_null($request->input('db_host')) ? env('SIIE_HOST', 'localhost') : $request->input('db_host');
      $bDocs = $request->input('docs');
      $bRows1 = $request->input('rows1');
      $bRows2 = $request->input('rows2');

      $this->sHost = $sDbHost;
      $items = 0;
      $partners = 0;
      $branches = 0;
      $adds = 0;
      $docs = 0;
      $rows1 = 0;
      $rows2 = 0;

      if (!is_null($bItems)) {
         $items = $this->importItems();
      }
      if (!is_null($bPartners)) {
         $partners = $this->importPartners();
      }
      if (!is_null($bBranches)) {
         $branches = $this->importBranches();
      }
      if (!is_null($bAddresses)) {
         $adds = $this->importAddresses();
      }
      if (!is_null($bDocs)) {
         $docs = $this->importDocuments($sDbName, $iYear);
      }
      if (!is_null($bRows1)) {
         $rows1 = $this->importDocumentRows($sDbName, $iYear);
      }

      return redirect()->route('siie.importation', [1, $items, $partners, $branches, $adds, $docs, $rows1, $rows2]);
   }

   

   public function importMms()
   {
      $response = SImportationsController::importFormulasAndPOs();

      if (is_array($response)) {
         $imports = [
            'formulas' => ($response[0] + $response[1]),
            'prod_orders' => ($response[2] > 0 ? $response[2] : 0)
         ];
      }
      else {
         $imports = [
            'formulas' => (-1),
            'prod_orders' => (-1)
         ];
      }

      return response()->json($imports);
   }

   public function importAll()
   {
      $oDbImport = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.DB_IMPORT'));

      $items = $this->importItems();
      $partners = $this->importPartners();
      $branches = $this->importBranches();
      $adds = $this->importAddresses();
      $docs = $this->importDocuments($oDbImport->val_text, session('work_date')->year);
      $rows1 = $this->importDocumentRows($oDbImport->val_text, session('work_date')->year);

      SDocumentsUtils::fixDocuments(session('work_date')->year);

      return [$items, $partners, $branches, $adds, $docs, $rows1];
   }

   public static function importFormulasAndPOs()
   {
      $oDbImport = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.DB_IMPORT'));
      $oDbHost = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.DB_HOST'));

      $forms = new SImportFormulas($oDbHost->val_text, $oDbImport->val_text);
      $nf = $forms->importFormulas();
      $formrows = new SImportFormulaRows($oDbHost->val_text, $oDbImport->val_text);
      $nfr = $formrows->importFormulaRows();
      $pos = new SImportProductionOrders($oDbHost->val_text, $oDbImport->val_text);
      $npos = $pos->importOrders();

      return [$nf, $nfr, $npos];
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