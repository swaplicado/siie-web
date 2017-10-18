<?php

namespace App\Http\Middleware;

use Closure;

use App\Menu\SErpMenu;
use App\Menu\SMmsMenu;
use App\Menu\SQmsMenu;
use App\Menu\SWmsMenu;
use App\Menu\STmsMenu;

class SMMenu {
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
          SErpMenu::createMenu();
          break;

        case \Config::get('scsys.MODULES.MMS'):
          SMmsMenu::createMenu();
          break;

        case \Config::get('scsys.MODULES.QMS'):
          SQmsMenu::createMenu();
          break;

        case \Config::get('scsys.MODULES.WMS'):
          SWmsMenu::createMenu();
          break;

        case \Config::get('scsys.MODULES.TMS'):
          STmsMenu::createMenu();
          break;

        default:
          # code...
          break;
      }

        return $next($request);
    }
}
