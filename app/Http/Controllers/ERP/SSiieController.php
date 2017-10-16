<?php namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\ERP\SYear;
use App\ERP\SMonth;
use App\SUtils\SUtil;
use App\SUtils\SValidation;
use App\SUtils\SMenu;
use App\SUtils\SProcess;

class SSiieController extends Controller
{

    public function __construct()
    {
       SProcess::constructor($this, \Config::get('scperm.PERMISSION.ERP'), \Config::get('scsys.MODULES.ERP'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        return view('siie.index');
    }
}
