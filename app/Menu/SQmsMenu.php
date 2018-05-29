<?php namespace App\Menu;

use Spatie\Menu\Laravel\Link;

class SQmsMenu {

  public static function createMenu()
  {
    \Menu::macro('main', function () {
      return \Menu::new()
        ->addClass('nav navbar-nav')
        ->link('', '')
        ->route('qms.home', trans('qms.MODULE'))
        ->route('qms.segregations.index', trans('qms.VIEW_INSPECTION'),
                                          [
                                            trans('qms.VIEW_INSPECTION'),
                                            \Config::get('scqms.SEGREGATION_TYPE.INSPECTED'),
                                            \Config::get('scqms.QMS_VIEW.INSPECTIONCLASSIFY'),
                                            \Config::get('scqms.TYPE_VIEW.BY_LOT')
                                          ])
          ->route('qms.segregations.index', trans('qms.VIEW_QUARENTINE'),
                                          [
                                            trans('qms.VIEW_QUARENTINE'),
                                            \Config::get('scqms.SEGREGATION_TYPE.QUARANTINE'),
                                            \Config::get('scqms.QMS_VIEW.QUARANTINECLASSIFY'),
                                            \Config::get('scqms.TYPE_VIEW.BY_LOT')
                                          ])
          ->route('qms.segregations.index', trans('qms.VIEW_INS_PALLET'),
                                          [
                                            trans('qms.VIEW_INS_PALLET'),
                                            \Config::get('scqms.SEGREGATION_TYPE.INSPECTED'),
                                            \Config::get('scqms.QMS_VIEW.INSPECTIONCLASSIFY'),
                                            \Config::get('scqms.TYPE_VIEW.BY_PALLET')
                                          ])
          ->route('qms.segregations.index', trans('qms.VIEW_QUA_PALLET'),
                                          [
                                            trans('qms.VIEW_QUA_PALLET'),
                                            \Config::get('scqms.SEGREGATION_TYPE.QUARANTINE'),
                                            \Config::get('scqms.QMS_VIEW.QUARANTINECLASSIFY'),
                                            \Config::get('scqms.TYPE_VIEW.BY_PALLET')
                                          ])

        ->route('qms.segregations.binnacle', trans('qms.BINNACLE'))
        ->wrap('div.collapse.navbar-collapse')
        ->setActiveFromRequest();
    });
  }
}
