<?php namespace App\Menu;

use Spatie\Menu\Laravel\Link;

class SWmsMenu {

  public static function createMenu()
  {
    \Menu::macro('main', function () {
      return \Menu::new()
        ->addClass('nav navbar-nav')
        ->link('', '')
        ->route('wms.home', trans('wms.MODULE'))
        ->submenu(
            Link::to('#', trans('wms.CATALOGUES').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->route('wms.whs.index', trans('wms.WAREHOUSES'))
                ->route('wms.locs.index', trans('wms.LOCATIONS'))
                ->html('', ['role' => 'separator', 'class' => 'divider'])
                ->link('#', trans('wms.PALLETS'))
                ->link('#', trans('wms.LOTS'))
                ->link('#', trans('wms.BAR_CODES'))
        )
        ->route('wms.movs.index', trans('wms.MOV_STK'))
        ->wrap('div.collapse.navbar-collapse')
        ->setActiveFromRequest();
    });
  }
}
