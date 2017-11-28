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
                ->addIf(session('location_enabled'), Link::toRoute('wms.locs.index', trans('wms.LOCATIONS')))
                ->html('', ['role' => 'separator', 'class' => 'divider'])
                ->route('wms.pallets.index', trans('wms.PALLETS'))
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
                ->route('wms.movs.create', trans('wms.MOV_WHS_TRS_OUT'), [\Config::get('scwms.MVT_TP_OUT_TRA')])
                ->route('wms.movs.create', trans('wms.PALLET_DIVISION'), [\Config::get('scwms.PALLET_RECONFIG_IN')])
                ->route('wms.movs.create', trans('wms.PALLET_ADD'), [\Config::get('scwms.PALLET_RECONFIG_OUT')])
        )
        ->submenu(
            Link::to('#', trans('wms.WHS_MOVS_QUERY').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->route('wms.movs.index', trans('wms.WHS_MOVS'))
        )
        ->submenu(
            Link::to('#', trans('wms.STOCK_QUERY').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->route('wms.stock.index', trans('wms.ITEM_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_ITEM')])
                ->route('wms.stock.index', trans('wms.PALLET_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_PALLET')])
                ->route('wms.stock.index', trans('wms.LOT_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_LOT')])
                ->addIf(session('location_enabled'), Link::toRoute('wms.stock.index', trans('wms.LOC_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_LOCATION')]))
                ->route('wms.stock.index', trans('wms.WHS_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_WAREHOUSE')])
                ->route('wms.stock.index', trans('wms.LOT_WHS_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE')])
                ->route('wms.stock.index', trans('wms.PALLET_LOT_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_PALLET_BY_LOT')])
        )
        ->wrap('div.collapse.navbar-collapse')
        ->setActiveFromRequest();
    });
  }
}
