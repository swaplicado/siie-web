<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\Menu\Laravel\Link;

class SMMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int $iModule can be:
     *          \Config::get('scsys.MODULES.ERP')
     *          \Config::get('scsys.MODULES.MMS')
     *          \Config::get('scsys.MODULES.QMS')
     *          \Config::get('scsys.MODULES.WMS')
     *          \Config::get('scsys.MODULES.TMS')
     * @return mixed
     */
    public function handle($request, Closure $next, $iModule)
    {
      switch ($iModule) {
        case \Config::get('scsys.MODULES.ERP'):
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

          break;

        case \Config::get('scsys.MODULES.MMS'):
          \Menu::macro('main', function () {
            return \Menu::new()
              ->addClass('nav navbar-nav')
              ->link('', '')
              ->route('mms.home', trans('mms.MODULE'))
              ->link('/two', 'Two')
              ->submenu(
                  Link::to('#', 'Dropdown <span class="caret"></span>')
                      ->addClass('dropdown-toggle')
                      ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
                  \Menu::new()
                      ->addClass('dropdown-menu')
                      ->link('#', 'Action')
                      ->link('#', 'Another action')
                      ->html('', ['role' => 'separator', 'class' => 'divider'])
              )
              ->wrap('div.collapse.navbar-collapse')
              ->setActiveFromRequest();
          });
          break;

        case \Config::get('scsys.MODULES.QMS'):
          \Menu::macro('main', function () {
            return \Menu::new()
              ->addClass('nav navbar-nav')
              ->link('', '')
              ->route('qms.home', trans('mms.MODULE'))
              ->link('/two', 'Two')
              ->submenu(
                  Link::to('#', 'Dropdown <span class="caret"></span>')
                      ->addClass('dropdown-toggle')
                      ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
                  \Menu::new()
                      ->addClass('dropdown-menu')
                      ->link('#', 'Action')
                      ->link('#', 'Another action')
                      ->html('', ['role' => 'separator', 'class' => 'divider'])
              )
              ->wrap('div.collapse.navbar-collapse')
              ->setActiveFromRequest();
          });
          break;

        case \Config::get('scsys.MODULES.WMS'):
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
          break;

        case \Config::get('scsys.MODULES.TMS'):
          \Menu::macro('main', function () {
            return \Menu::new()
              ->addClass('nav navbar-nav')
              ->link('', '')
              ->route('tms.home', trans('mms.MODULE'))
              ->link('/two', 'Two')
              ->submenu(
                  Link::to('#', 'Dropdown <span class="caret"></span>')
                      ->addClass('dropdown-toggle')
                      ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
                  \Menu::new()
                      ->addClass('dropdown-menu')
                      ->link('#', 'Action')
                      ->link('#', 'Another action')
                      ->html('', ['role' => 'separator', 'class' => 'divider'])
              )
              ->wrap('div.collapse.navbar-collapse')
              ->setActiveFromRequest();
          });
          break;

        default:
          # code...
          break;
      }

        return $next($request);
    }
}
