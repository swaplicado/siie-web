<?php namespace App\Menu;

use Spatie\Menu\Laravel\Link;

class SMmsMenu {

  public static function createMenu()
  {
    \Menu::macro('main', function () {
      return \Menu::new()
        ->addClass('nav navbar-nav')
        ->link('', '')
        ->route('mms.home', trans('mms.MODULE'))
        ->route('mms.formulas.index', trans('mms.FORMULAS'))
        ->wrap('div.collapse.navbar-collapse')
        ->setActiveFromRequest();
    });
  }
}
