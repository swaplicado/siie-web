<?php namespace App\SUtils;
    
use App\Http\Controllers\ERP\SImportationsController;

class SImportUtils
{
    public static function synchronize()
    {
        $import = new SImportationsController();
        $import->importAll();
        SImportationsController::importFormulasAndPOs();
    }
}

