<?php namespace App\Http\Controllers\SYS;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SValidation;
use App\SUtils\SUtil;
use App\SUtils\SSessionUtils;
use App\SUtils\SConnectionUtils;
use App\SYS\SCompany;
use App\SYS\SConfiguration;
use App\ERP\SErpConfiguration;
use App\Database\Config;
use App\ERP\SPartner;
use App\ERP\SCurrency;
use App\SYS\SUserCompany;
use App\ERP\SUserBranch;
use App\ERP\SUserWhs;
use App\ERP\SBranch;
use App\ERP\SYear;
use App\WMS\SWarehouse;
use App\SCore\SStockManagment;
use App\SCore\SSegregationCore;
use App\SPadLocks\SRecordLock;

class SStartController extends Controller
{

    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $oUtils = new SSessionUtils();
        $oDbConfig = new Config();
        $oLocks = new SRecordLock();

        session(['utils' => $oUtils]);
        session(['db_configuration' => $oDbConfig]);
        session(['lock' => $oLocks]);

        $lUserCompany = SUtil::getUserCompany(\Auth::user());

        if (sizeof($lUserCompany) < 1 && ! session('utils')->isSuperUser(\Auth::user())) {
          return redirect()->route('notauthorizedsys');
        }

        $oPartner = $this->getPartnerByCompany($lUserCompany[0]->company_id);

        $lBranches= $this->getBranches($oPartner->id_partner);

        $iCompany = $lUserCompany[0]->company_id;
        $iBranch = 0;
        $iWarehouse = 0;

        if (sizeof($lBranches) > 0) {
          $oBranch = $lBranches[0];
          $lWhs = $this->getWarehouses($oBranch->id_branch);

          $iBranch = $oBranch->id_branch;
          $iWarehouse = sizeof($lWhs) > 0 ? $lWhs[0]->id_whs : 0;
        }
        else {
          $lBranches = array();
          $lWhs = array();
        }

        return view('start.select')
                  ->with('iCompany', session()->has('company') ? session('company')->id_company : $iCompany)
                  ->with('iBranch', session()->has('branch') ? session('branch')->id_branch : $iBranch)
                  ->with('iWarehouse', session()->has('whs') ? session('whs')->id_whs : $iWarehouse)
                  ->with('lWhs', $lWhs)
                  ->with('lBranches', $lBranches)
                  ->with('lUserCompany', $lUserCompany);
    }

    public function changeCompany($idCompany)
    {
      $branches = $this->getBranches($this->getPartnerByCompany($idCompany)->id_partner);

      return $branches;
    }
    public function changeBranch($iCompany, $idBranch)
    {
      $oCompany = SCompany::find($iCompany);
      SConnectionUtils::reconnectCompany($oCompany->database_name);

      return $this->getWarehouses($idBranch);
    }

    private function getBranches($idPartner)
    {
      $lUserBranch = \DB::connection(session('db_configuration')->getConnCompany())
                          ->table('erpu_branches AS eb')
                          ->where('partner_id', '=', $idPartner);

      if (! session('utils')->isSuperUser(\Auth::user()))
      {
        $lUserBranch = $lUserBranch->join('erpu_access_branch eab', 'eab.branch_id', '=', 'eb.id_branch')
                                    ->where('user_id', \Auth::user()->id);
      }

      $lBranches = $lUserBranch->where('eb.is_deleted', false)
                                ->select('eb.name', 'eb.id_branch')
                                ->orderBy('eb.name')
                                ->get();

      return $lBranches;
    }

    private function getWarehouses(int $idBranch)
    {
      $lWhs = SWarehouse::where('is_deleted', false)
                      ->where('branch_id', $idBranch)
                      ->get();

      return $lWhs;
    }

    private function getPartnerByCompany(int $idCompany)
    {
       return session('utils')->getPartnerByCompany($idCompany);
    }

    /**
     *
     */
    public function getIn(Request $request)
    {
        $oCompany = SCompany::find($request->company);
        session(['company' => $oCompany]);
        
        SConnectionUtils::reconnectCompany($oCompany->database_name);

        session(['branch' => SBranch::find($request->branch)]);
        session(['whs' => SWarehouse::find($request->whs)]);

        $oErpConfigurationPartner = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.PARTNER_ID'));
        $oErpConfLocCur = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.LOCAL_CURRENCY'));
        $oDecAmount = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.DECIMALS_AMT'));
        $oDecQuantity = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.DECIMALS_QTY'));
        $oDecPercent = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.DECIMALS_PERC'));
        $oLongFolios = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.FOLIOS_LONG'));
        $oLocationEn = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.LOC_ENABLED'));
        $olockTime = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.LOCK_TIME'));
        $oIdTranWhs = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.WHS_ITEM_TRANSIT'));
        $oNumberLabels = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.PALLETS'));
        $oLongFQlty = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.QLTY_DOCS_FOLIOS_LONG'));

        $oPartner = SPartner::find($oErpConfigurationPartner->val_int);
        $oCurrency = SCurrency::find($oErpConfLocCur->val_int);
        $oTransitWarehouse = SWarehouse::find($oIdTranWhs->val_int);
        $oStock = new SStockManagment();
        $oSegregations = new SSegregationCore();

        session(['partner' => $oPartner]);
        session(['currency' => $oCurrency]);
        session(['decimals_amt' => $oDecAmount->val_int]);
        session(['decimals_qty' => $oDecQuantity->val_int]);
        session(['decimals_percent' => $oDecPercent->val_int]);
        session(['long_folios' => $oLongFolios->val_int]);
        session(['location_enabled' => $oLocationEn->val_boolean]);
        session(['lock_time' => $olockTime->val_int]);
        session(['stock' => $oStock]);
        session(['segregation' => $oSegregations]);
        $oTransitWarehouse->locations;
        session(['transit_whs' => $oTransitWarehouse]);
        session(['number_label' => $oNumberLabels->val_int]);
        session(['qltydocs_folios' => $oLongFQlty->val_int]);

	      $sWorkDate =  $request->work_date;
        $oWorkDate = Carbon::parse($sWorkDate);
        session(['work_date' => $oWorkDate]);

        $iYear = $oWorkDate->year;
        $oYear = SYear::where('year', $iYear)
                        ->where('is_deleted', false)
                        ->first();
        session(['work_year' => $oYear->id_year]);

        session('utils')->setUserPermissions();

        return SStartController::selectModule();
    }

    /**
     *
     */
    public function selectModule()
    {
        session('utils')->setUserPermissions();

        return view('start.modules');
    }
}
