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
        ->route('qms.segregations.index', trans('wms.STK_SEG_QLTY'), [trans('wms.STK_SEG_QLTY'), \Config::get('scqms.SEGREGATION_TYPE.QUALITY')])
        ->wrap('div.collapse.navbar-collapse')
        ->setActiveFromRequest();
    });
  }
}
