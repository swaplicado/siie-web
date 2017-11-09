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
                ->route('wms.lots.index', trans('wms.LOTS'))
                ->route('wms.codes.start', trans('wms.BAR_CODES'))
        )
        ->submenu(
            Link::to('#', trans('wms.MOV_STK').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->route('wms.movs.create', trans('wms.MOV_STK_IN_ADJ'), [\Config::get('scwms.MVT_TP_IN_ADJ')])
                ->route('wms.movs.create', trans('wms.MOV_STK_OUT_ADJ'), [\Config::get('scwms.MVT_TP_OUT_ADJ')])
        )
        ->submenu(
            Link::to('#', trans('wms.STOCK_QUERY').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->route('wms.stock.index', 'Existencias por ítem')
        )
        ->wrap('div.collapse.navbar-collapse')
        ->setActiveFromRequest();
    });
  }
}
