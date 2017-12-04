<?php namespace App\Menu;

use Spatie\Menu\Laravel\Link;

class SErpMenu {

  public static function createMenu()
  {
    \Menu::macro('main', function () {
      return \Menu::new()
        ->addClass('nav navbar-nav')
        ->link('', '')
        ->route('siie.home', trans('siie.HOME'))
        ->route('siie.families.index', trans('siie.FAMILIES'))
        ->route('siie.groups.index', trans('siie.GROUPS'))
        ->submenu(
          Link::to('#', trans('siie.GENDERS').'<span class="caret"></span>')
              ->addClass('dropdown-toggle')
              ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
          \Menu::new()
              ->addClass('dropdown-menu')
              ->route('siie.genders.index', trans('siie.MATERIALS'), [\Config::get('scsiie.ITEM_CLS.MATERIAL')])
              ->route('siie.genders.index', trans('siie.PRODUCTS'), [\Config::get('scsiie.ITEM_CLS.PRODUCT')])
              ->route('siie.genders.index', trans('siie.SPENDING'), [\Config::get('scsiie.ITEM_CLS.SPENDING')])
        )
        ->submenu(
            Link::to('#', trans('siie.ITEMS').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->route('siie.items.index', trans('siie.MATERIALS'), [\Config::get('scsiie.ITEM_CLS.MATERIAL')])
                ->route('siie.items.index', trans('siie.PRODUCTS'), [\Config::get('scsiie.ITEM_CLS.PRODUCT')])
                ->route('siie.items.index', trans('siie.SPENDING'), [\Config::get('scsiie.ITEM_CLS.SPENDING')])
        )
        ->submenu(
            Link::to('#', trans('siie.CONFIGURATION').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->route('siie.branches.index', trans('siie.BRANCHES'))
                ->html('', ['role' => 'separator', 'class' => 'divider'])
                ->route('siie.branches.index', trans('siie.ACG_YEAR_PER'))
                ->html('', ['role' => 'separator', 'class' => 'divider'])
                ->route('siie.bps.index', trans('siie.BPS'))
        )
        ->route('siie.units.index', trans('siie.UNITS'))
        ->route('siie.units.index', trans('siie.CONVERTIONS'))
        ->wrap('div.collapse.navbar-collapse')
        ->setActiveFromRequest();
    });
  }
}