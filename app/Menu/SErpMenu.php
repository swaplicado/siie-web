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
        ->submenu(
            Link::to('#', trans('siie.CONFIGURATION').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->route('siie.branches.index', trans('siie.BRANCHES'))
                ->route('siie.branches.index', trans('siie.ACG_YEAR_PER'))
                ->html('', ['role' => 'separator', 'class' => 'divider'])
                ->route('siie.bps.index', trans('siie.BPS'))
        )
        ->submenu(
            Link::to('#', trans('siie.CATALOGUES').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->route('siie.bps.index', trans('siie.MATERIALS'))
                ->route('siie.bps.index', trans('siie.PRODUCTS'))
                ->route('siie.genders.index', trans('siie.GENDERS'))
                ->route('siie.groups.index', trans('siie.GROUPS'))
                ->route('siie.families.index', trans('siie.FAMILIES'))
                ->route('siie.units.index', trans('siie.UNITS'))
                ->route('siie.units.index', trans('siie.CONVERTIONS'))
                ->html('', ['role' => 'separator', 'class' => 'divider'])
        )
        ->wrap('div.collapse.navbar-collapse')
        ->setActiveFromRequest();
    });
  }
}
