<?php

namespace App\Http\Middleware;

use Closure;

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
          \Menu::make('sMenu', function($menu) {
              $menu->add('Home', ['route' => 'mms.home']);
              $menu->add(trans('siie.ERP'), '')->nickname(trans('siie.ERP'));
              $menu->get(trans('siie.ERP'))->add(trans('siie.ERP_COMPANIES'), ['route' => 'siie.companies.index']);
              $menu->get(trans('siie.ERP'))->add(trans('siie.BRANCHES'), ['route' => 'siie.branches.index']);
              $menu->get(trans('siie.ERP'))->add(trans('siie.ACG_YEAR_PER'), ['route' => 'siie.years.index']);
              $menu->get(trans('siie.ERP'))->add(trans('siie.BPS'), ['route' => 'siie.bps.index']);
              $menu->add(trans('siie.CATALOGUES'), '')->nickname(trans('siie.CATALOGUES'));
              $menu->add(trans('siie.ITEMS'), 'what-we-do')->nickname(trans('siie.ITEMS'));
              $menu->get(trans('siie.ITEMS'))->add(trans('siie.MATERIALS'), 'what-we-do');
              $menu->get(trans('siie.ITEMS'))->add(trans('siie.PRODUCTS'), 'what-we-do');
              $menu->get(trans('siie.ITEMS'))->add(trans('siie.GENDERS'), ['route' => 'siie.genders.index']);
              $menu->get(trans('siie.ITEMS'))->add(trans('siie.GROUPS'), ['route' => 'siie.groups.index']);
              $menu->get(trans('siie.ITEMS'))->add(trans('siie.FAMILIES'), ['route' => 'siie.families.index']);
              $menu->get(trans('siie.ITEMS'))->add(trans('siie.UNITS'), ['route' => 'siie.units.index']);
              $menu->get(trans('siie.ITEMS'))->add(trans('siie.CONVERTIONS'), 'what-we-do');
          });

          break;

        case \Config::get('scsys.MODULES.MMS'):
          \Menu::make('sMenu', function($menu) {
              $menu->add(' ');
              $menu->add('Home', ['route' => 'mms.home']);
              $menu->add('About',    'about');
              $menu->add('Services', 'services');
              $menu->add('Contact',  'contact');
              $menu->add(trans('wms.REPORTS'), 'what-we-do');
          });
          break;

        case \Config::get('scsys.MODULES.QMS'):
          \Menu::make('sMenu', function($menu) {
              $menu->add(' ');
              $menu->add('Home', ['route' => 'qms.home']);
              $menu->add('About',    'about');
              $menu->add('Services', 'services');
              $menu->add('Contact',  'contact');
              $menu->add(trans('wms.REPORTS'), 'what-we-do');

          });
          break;

        case \Config::get('scsys.MODULES.WMS'):
          \Menu::make('sMenu', function($menu) {
              $menu->add(' ');
              $menu->add(trans('userinterface.HOME'), ['route' => 'wms.home']);
              $menu->add(trans('wms.CONFIG'), 'what-we-do')->nickname(trans('wms.CONFIG'));
              $menu->get(trans('wms.CONFIG'))->add(trans('wms.CONFIG'), 'what-we-do');
              $menu->get(trans('wms.CONFIG'))->add(trans('wms.CONFIG'), 'what-we-do');
              $menu->add(trans('wms.CATALOGUES'), 'what-we-do')->nickname(trans('wms.CATALOGUES'));
              $menu->get(trans('wms.CATALOGUES'))->add(trans('wms.WAREHOUSES'), ['route' => 'wms.whs.index']);
              $menu->get(trans('wms.CATALOGUES'))->add(trans('wms.LOCATIONS'), ['route' => 'wms.locs.index']);
              $menu->get(trans('wms.CATALOGUES'))->add(trans('wms.PALLETS'), 'what-we-do');
              $menu->get(trans('wms.CATALOGUES'))->add(trans('wms.LOTS'), 'what-we-do');
              $menu->get(trans('wms.CATALOGUES'))->add(trans('wms.BAR_CODES'), 'what-we-do');
              $menu->add(trans('wms.INVENTORY'), 'what-we-do');
              $menu->add(trans('wms.REPORTS'), 'what-we-do');

          });
          break;

        case \Config::get('scsys.MODULES.TMS'):
          \Menu::make('sMenu', function($menu) {
              $menu->add(' ');
              $menu->add('Home', ['route' => 'tms.home']);
              $menu->add('About',    'about');
              $menu->add('Services', 'services');
              $menu->add('Contact',  'contact');
              $menu->add(trans('wms.REPORTS'), 'what-we-do');

          });
          break;

        default:
          # code...
          break;
      }

        return $next($request);
    }
}
