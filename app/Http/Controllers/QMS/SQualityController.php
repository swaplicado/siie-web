<?php namespace App\Http\Controllers\QMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SProcess;

use App\SUtils\SUtil;
use App\SUtils\SMenu;

class SQualityController extends Controller
{
    private $oCurrentAssignament;
    private $iFilter;

    public function __construct()
    {
       SProcess::constructor($this, \Config::get('scperm.PERMISSION.QMS'), \Config::get('scsys.MODULES.QMS'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        return view('qms.index');
    }
}
