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
        ->route('qms.segregations.index', trans('wms.STK_SEG_QLTY'),
                                          [
                                            trans('wms.STK_SEG_QLTY'),
                                            \Config::get('scqms.SEGREGATION_TYPE.INSPECTED'),
                                            \Config::get('scqms.QMS_VIEW.BY_STATUS')
                                          ])
        ->route('qms.segregations.index', trans('qms.VIEW_INSPECTION'),
                                          [
                                            trans('wms.STK_SEG_QLTY'),
                                            \Config::get('scqms.SEGREGATION_TYPE.INSPECTED'),
                                            \Config::get('scqms.QMS_VIEW.INSPECTIONCLASSIFY')
                                          ])
        ->route('qms.segregations.index', trans('qms.VIEW_QUARENTINE'),
                                          [
                                            trans('wms.STK_SEG_QLTY'),
                                            \Config::get('scqms.SEGREGATION_TYPE.QUARANTINE'),
                                            \Config::get('scqms.QMS_VIEW.QUARANTINECLASSIFY')
                                          ])
        ->wrap('div.collapse.navbar-collapse')
        ->setActiveFromRequest();
    });
  }
}
