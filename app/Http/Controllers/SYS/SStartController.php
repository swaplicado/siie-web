<?php namespace App\Http\Controllers\SYS;

use App\Database\Config;
use App\ERP\SBranch;
use App\ERP\SCurrency;
use App\ERP\SErpConfiguration;
use App\ERP\SPartner;
use App\ERP\SYear;
use App\Http\Controllers\Controller;
use App\SCore\SSegregationCore;
use App\SCore\SStockManagment;
use App\SPadLocks\SRecordLock;
use App\SUtils\SConnectionUtils;
use App\SUtils\SImportUtils;
use App\SUtils\SSessionUtils;
use App\SUtils\SUtil;
use App\SYS\SCompany;
use App\WMS\SWarehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SStartController extends Controller
{

    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
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

        $lCompanies = array();

        foreach ($lUserCompany as $usrCompany) {
          $oCompany = $usrCompany->company;
          $oCompany->oPartner = $this->getPartnerByCompany($oCompany->id_company);
          $oCompany->oPartner->lBranches = collect($this->getBranches($oCompany->oPartner->id_partner))->keyBy('id_branch')->sortBy('name');

          if (sizeof($oCompany->oPartner->lBranches) > 0) {
            foreach ($oCompany->oPartner->lBranches as $branch) {
              $branch->lWhs = collect($this->getWarehouses($branch->id_branch))->keyBy('id_whs')->sortBy('whs_code');
            }
          }

          $lCompanies[$oCompany->id_company] = $oCompany;
        }

        $iCompany = 0;
        $iBranch = 0;
        $iWarehouse = 0;

        if (sizeof($lCompanies) > 0) {
          $iCompany = $lUserCompany[0]->company_id;

          if (sizeof($lCompanies[$iCompany]->oPartner->lBranches) > 0) {
            $first_value = reset($lCompanies[$iCompany]->oPartner->lBranches);
            $iBranch = key($first_value);

            if (sizeof($lCompanies[$iCompany]->oPartner->lBranches[$iBranch]->lWhs) > 0) {
              $first_valueb = reset($lCompanies[$iCompany]->oPartner->lBranches[$iBranch]->lWhs);
              $iWarehouse = key($first_valueb);
            }
          }
        }

        return view('start.select')
                  ->with('iCompany', session()->has('company') ? session('company')->id_company : $iCompany)
                  ->with('iBranch', session()->has('branch') ? session('branch')->id_branch : $iBranch)
                  ->with('iWarehouse', session()->has('whs') ? session('whs')->id_whs : $iWarehouse)
                  ->with('lCompanies', $lCompanies);
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
        $lUserBranch = $lUserBranch->join('erpu_access_branch AS eab', 'eab.branch_id', '=', 'eb.id_branch')
                                    ->where('user_id', \Auth::user()->id);
      }

      $lBranches = $lUserBranch->where('eb.is_deleted', false)
                                ->select('eb.name', 'eb.id_branch')
                                ->orderBy('eb.name', 'ASC')
                                ->get();

      return $lBranches;
    }

    private function getWarehouses(int $idBranch)
    {
      $lWhs = SWarehouse::where('is_deleted', false)
                      ->select('code AS whs_code', 'name AS whs_name', 'id_whs')
                      ->where('branch_id', $idBranch)
                      ->orderBy('whs_code', 'ASC')
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

        SImportUtils::synchronize();

        return view('start.modules');
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
