<?php namespace App\Menu;

use Spatie\Menu\Laravel\Link;
use App\SUtils\SValidation;

class SMmsMenu {

  public static function createMenu()
  {
    \Menu::macro('main', function () {
      return \Menu::new()
        ->addClass('nav navbar-nav')
        ->link('', '')
        ->route('mms.home', trans('mms.MODULE'))
        ->submenu(
            Link::to('#', trans('mms.PRODUCTION').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.MMS_FLOORS')),
                          Link::toRoute('mms.floors.index', trans('mms.FLOORS'), [0]))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.MMS_PRODUCTION_PLANES')),
                          Link::toRoute('mms.planes.index', trans('mms.PRODUCTION_PLANES'), [0]))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.MMS_PRODUCTION_ORDERS')),
                          Link::toRoute('mms.orders.index', trans('mms.PRODUCTION_ORDERS'), [0]))
         )
        ->submenu(
            Link::to('#', trans('mms.FORMULAS').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.MMS_FORMULAS')),
                          Link::toRoute('mms.formulas.index', trans('mms.labels.FORMULAS')))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.MMS_FORMULAS')),
                          Link::toRoute('mms.formulas.indexdetail', trans('mms.labels.FORMULAS_DETAIL')))
         )
        ->route('mms.explosion.index', trans('mms.EXPLOSION_MATERIALS'))
        ->submenu(
            Link::to('#', trans('mms.MVTS_QUERY').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.MMS_PRODUCTION_QUERYS')),
                          Link::toRoute('mms.movs.show', trans('mms.movs.RM_DELIVERY'), [\Config::get('scmms.MOVS_QUERY.RM_DELIVERY'),
                                                                                                trans('mms.movs.RM_DELIVERY')]))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.MMS_PRODUCTION_QUERYS')),
                          Link::toRoute('mms.movs.show', trans('mms.movs.RM_RETURN'), [\Config::get('scmms.MOVS_QUERY.RM_RETURN'),
                                                                                                trans('mms.movs.RM_RETURN')]))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.MMS_PRODUCTION_QUERYS')),
                          Link::toRoute('mms.movs.show', trans('mms.movs.PM_DELIVERY'), [\Config::get('scmms.MOVS_QUERY.PM_DELIVERY'),
                                                                                                trans('mms.movs.PM_DELIVERY')]))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.MMS_PRODUCTION_QUERYS')),
                          Link::toRoute('mms.movs.show', trans('mms.movs.PM_RETURN'), [\Config::get('scmms.MOVS_QUERY.PM_RETURN'),
                                                                                                trans('mms.movs.PM_RETURN')]))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.MMS_PRODUCTION_QUERYS')),
                          Link::toRoute('mms.movs.show', trans('mms.movs.PP_DELIVERY'), [\Config::get('scmms.MOVS_QUERY.PP_DELIVERY'),
                                                                                                trans('mms.movs.PP_DELIVERY')]))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.MMS_PRODUCTION_QUERYS')),
                          Link::toRoute('mms.movs.show', trans('mms.movs.PP_ASSIGNAMENT'), [\Config::get('scmms.MOVS_QUERY.PP_ASSIGNAMENT'),
                                                                                                trans('mms.movs.PP_ASSIGNAMENT')]))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.MMS_PRODUCTION_QUERYS')),
                          Link::toRoute('mms.movs.show', trans('mms.movs.FP_DELIVERY'), [\Config::get('scmms.MOVS_QUERY.FP_DELIVERY'),
                                                                                                trans('mms.movs.FP_DELIVERY')]))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.MMS_PRODUCTION_QUERYS')),
                          Link::toRoute('mms.movs.show', trans('mms.movs.CONSUMPTION_MVTS'), [\Config::get('scmms.MOVS_QUERY.CONSUMPTION_MVTS'),
                                                                                                trans('mms.movs.CONSUMPTION_MVTS')]))
         )
        ->wrap('div.collapse.navbar-collapse')
        ->setActiveFromRequest();
    });
  }
}
