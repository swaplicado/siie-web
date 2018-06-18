<?php namespace App\Menu;

use Spatie\Menu\Laravel\Link;
use App\SUtils\SValidation;

class SWmsMenu {

  public static function createMenu()
  {
    \Menu::macro('main', function () {
      return \Menu::new()
        ->addClass('nav navbar-nav')
        ->link('', '')
        ->route('wms.home', trans('wms.MODULE'))
        ->submenu(
            Link::to('#', trans('wms.CONFIGURATION').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.CONFIG_WHS_MNG')),
                          Link::toRoute('wms.folios.index', trans('wms.WHS_MOVS_FOLIOS')))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.CONFIG_WHS_MNG')),
                          Link::toRoute('wms.limits.index', trans('userinterface.titles.LIST_LIMITS')))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.CONFIG_WHS_MNG')),
                          Link::toRoute('wms.itemcontainers.index', trans('userinterface.titles.LIST_ITEM_CONTAINERS')))
                ->html('', ['role' => 'separator', 'class' => 'divider'])
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.CONFIG_WHS_MNG')),
                          Link::toRoute('wms.whs.index', trans('wms.WAREHOUSES')))
                ->addIf(session('location_enabled') && SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.CONFIG_WHS_MNG')),
                          Link::toRoute('wms.locs.index', trans('wms.LOCATIONS')))
                ->html('', ['role' => 'separator', 'class' => 'divider'])
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.CONFIG_WHS_STD')),
                          Link::toRoute('wms.pallets.index', trans('wms.PALLETS'), [0, '']))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.CONFIG_WHS_STD')),
                          Link::toRoute('wms.lots.index', trans('wms.LOTS')))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.CONFIG_WHS_STD')),
                          Link::toRoute('wms.codes.start', trans('userinterface.titles.GENERATION_BARCODE')))
        )
        ->submenu(
            Link::to('#', trans('wms.WHS_MOVS_QUERY').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->route('wms.movs.index', trans('wms.WHS_MOVS'))
                ->route('wms.movs.indexdetail', trans('wms.WHS_MOVS_DETAIL'))
                ->route('wms.movs.docs', trans('wms.WHS_DOCS'))
                ->html('', ['role' => 'separator', 'class' => 'divider'])
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.ADJUSTS')),
                        Link::toRoute('wms.movs.create', trans('wms.MOV_STK_IN_ADJ'), [\Config::get('scwms.MVT_TP_IN_ADJ'), trans('wms.MOV_STK_IN_ADJ')]))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.ADJUSTS')),
                        Link::toRoute('wms.movs.create', trans('wms.MOV_STK_OUT_ADJ'), [\Config::get('scwms.MVT_TP_OUT_ADJ'),  trans('wms.MOV_STK_OUT_ADJ')]))
                ->html('', ['role' => 'separator', 'class' => 'divider'])
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.TRANSFERS')),
                        Link::toRoute('wms.movs.create', trans('wms.MOV_WHS_INTERNAL_TRS_OUT'), [\Config::get('scwms.MVT_TP_OUT_TRA'), trans('wms.MOV_WHS_INTERNAL_TRS_OUT')]))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.TRANSFERS_EXTERNAL')),
                        Link::toRoute('wms.movs.create', trans('wms.MOV_WHS_SEND_EXTERNAL_TRS_OUT'), [\Config::get('scwms.MVT_TP_OUT_TRA'), trans('wms.MOV_WHS_SEND_EXTERNAL_TRS_OUT')]))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.TRANSFERS_EXTERNAL')),
                        Link::toRoute('wms.movs.receptions', trans('wms.MOV_WHS_RECEIVE_EXTERNAL_TRS_OUT')))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.TRANSFERS_EXTERNAL')),
                        Link::toRoute('wms.movs.transferred', 'Traspasos externos enviados'))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.TRANSFERS_EXTERNAL')),
                        Link::toRoute('wms.movs.received', 'Traspasos externos recibidos'))
                ->html('', ['role' => 'separator', 'class' => 'divider'])
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.PALLET_RECONFIG')),
                        Link::toRoute('wms.movs.create', trans('wms.PALLET_DIVISION'), [\Config::get('scwms.PALLET_RECONFIG_IN'), trans('wms.PALLET_DIVISION')]))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.PALLET_RECONFIG')),
                        Link::toRoute('wms.movs.create', trans('wms.PALLET_ADD'), [\Config::get('scwms.PALLET_RECONFIG_OUT'), trans('wms.PALLET_ADD')]))
                ->html('', ['role' => 'separator', 'class' => 'divider'])
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.INVENTORY_OPERATION')),
                        Link::toRoute('wms.inventory.emptywarehouse', trans('wms.EMPTY_WAREHOUSE')))
                ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.INVENTORY_OPERATION')),
                        Link::toRoute('wms.inventory.initialinventory', trans('wms.GENERATE_INITIAL_INVENTORY')))
        )
        ->submenu(
            Link::to('#', trans('wms.PUR_DOCS').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->submenu(
                    Link::to('#', trans('actions.SUPPLY').'<span class="glyphicon glyphicon-triangle-right btn-xs"></span>')
                        ->addClass('test'),
                    \Menu::new()
                        ->addParentClass('dropdown-submenu')
                        ->addClass('dropdown-menu')
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_PURCHASES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_OR_PUR_BY_SUPP'),
                                                            [\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                            \Config::get('scsiie.DOC_CLS.ORDER'),
                                                            \Config::get('scsiie.DOC_TYPE.ORDER'),
                                                            \Config::get('scwms.DOC_VIEW.NORMAL'),
                                                            \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                            trans('userinterface.titles.LIST_OR_PUR_BY_SUPP')]))
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_PURCHASES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_OR_PUR_BY_SUPP_DET'),
                                                            [\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                            \Config::get('scsiie.DOC_CLS.ORDER'),
                                                            \Config::get('scsiie.DOC_TYPE.ORDER'),
                                                            \Config::get('scwms.DOC_VIEW.DETAIL'),
                                                            \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                            trans('userinterface.titles.LIST_OR_PUR_BY_SUPP_DET')]))
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_PURCHASES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_INVS_PUR_BY_SUPP'),
                                                            [\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                            \Config::get('scsiie.DOC_CLS.DOCUMENT'),
                                                            \Config::get('scsiie.DOC_TYPE.INVOICE'),
                                                            \Config::get('scwms.DOC_VIEW.NORMAL'),
                                                            \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                            trans('userinterface.titles.LIST_INVS_PUR_BY_SUPP')]))
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_PURCHASES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_INVS_PUR_BY_SUPP_DET'),
                                                            [\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                            \Config::get('scsiie.DOC_CLS.DOCUMENT'),
                                                            \Config::get('scsiie.DOC_TYPE.INVOICE'),
                                                            \Config::get('scwms.DOC_VIEW.DETAIL'),
                                                            \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                            trans('userinterface.titles.LIST_INVS_PUR_BY_SUPP_DET')]))
                )
                ->submenu(
                    Link::to('#', trans('actions.DEVOLVE').'<span class="glyphicon glyphicon-triangle-right btn-xs"></span>')
                        ->addClass('test'),
                    \Menu::new()
                        ->addParentClass('dropdown-submenu')
                        ->addClass('dropdown-menu')
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_PURCHASES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_CN_PUR_BY_SUPP'),
                                                            [\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                            \Config::get('scsiie.DOC_CLS.ADJUST'),
                                                            \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE'),
                                                            \Config::get('scwms.DOC_VIEW.NORMAL'),
                                                            \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                            trans('userinterface.titles.LIST_CN_PUR_BY_SUPP')]))
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_PURCHASES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_CN_PUR_BY_SUPP_DET'),
                                                            [\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                            \Config::get('scsiie.DOC_CLS.ADJUST'),
                                                            \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE'),
                                                            \Config::get('scwms.DOC_VIEW.DETAIL'),
                                                            \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                            trans('userinterface.titles.LIST_CN_PUR_BY_SUPP_DET')]))
                )
                ->submenu(
                    Link::to('#', 'Surtido'.'<span class="glyphicon glyphicon-triangle-right btn-xs"></span>')
                        ->addClass('test'),
                    \Menu::new()
                        ->addParentClass('dropdown-submenu')
                        ->addClass('dropdown-menu')
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_PURCHASES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_OR_PUR_SUPP'),
                                                            [\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                            \Config::get('scsiie.DOC_CLS.ORDER'),
                                                            \Config::get('scsiie.DOC_TYPE.ORDER'),
                                                            \Config::get('scwms.DOC_VIEW.NORMAL'),
                                                            \Config::get('scwms.DOC_VIEW_S.SUPP'),
                                                            trans('userinterface.titles.LIST_OR_PUR_SUPP')
                                                          ]))
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_PURCHASES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_OR_PUR_SUPP_DET'),
                                                          [\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                          \Config::get('scsiie.DOC_CLS.ORDER'),
                                                          \Config::get('scsiie.DOC_TYPE.ORDER'),
                                                          \Config::get('scwms.DOC_VIEW.DETAIL'),
                                                          \Config::get('scwms.DOC_VIEW_S.SUPP'),
                                                          trans('userinterface.titles.LIST_OR_PUR_SUPP_DET')
                                                        ]))
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_PURCHASES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_INVS_PUR_SUPP'),
                                                            [\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                            \Config::get('scsiie.DOC_CLS.DOCUMENT'),
                                                            \Config::get('scsiie.DOC_TYPE.INVOICE'),
                                                            \Config::get('scwms.DOC_VIEW.NORMAL'),
                                                            \Config::get('scwms.DOC_VIEW_S.SUPP'),
                                                            trans('userinterface.titles.LIST_INVS_PUR_SUPP')
                                                          ]))
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_PURCHASES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_INVS_PUR_SUPP_DET'),
                                                          [\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                          \Config::get('scsiie.DOC_CLS.DOCUMENT'),
                                                          \Config::get('scsiie.DOC_TYPE.INVOICE'),
                                                          \Config::get('scwms.DOC_VIEW.DETAIL'),
                                                          \Config::get('scwms.DOC_VIEW_S.SUPP'),
                                                          trans('userinterface.titles.LIST_INVS_PUR_SUPP_DET')
                                                        ]))
                )
        )
        ->submenu(
            Link::to('#', trans('wms.SAL_DOCS').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->submenu(
                    Link::to('#', trans('actions.SUPPLY').'<span class="glyphicon glyphicon-triangle-right btn-xs"></span>')
                        ->addClass('test'),
                    \Menu::new()
                        ->addParentClass('dropdown-submenu')
                        ->addClass('dropdown-menu')
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_SALES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_OR_SAL_BY_SUPP'),
                                                            [\Config::get('scsiie.DOC_CAT.SALES'),
                                                            \Config::get('scsiie.DOC_CLS.ORDER'),
                                                            \Config::get('scsiie.DOC_TYPE.ORDER'),
                                                            \Config::get('scwms.DOC_VIEW.NORMAL'),
                                                            \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                            trans('userinterface.titles.LIST_OR_SAL_BY_SUPP')]))
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_SALES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_OR_SAL_BY_SUPP_DET'),
                                                            [\Config::get('scsiie.DOC_CAT.SALES'),
                                                            \Config::get('scsiie.DOC_CLS.ORDER'),
                                                            \Config::get('scsiie.DOC_TYPE.ORDER'),
                                                            \Config::get('scwms.DOC_VIEW.DETAIL'),
                                                            \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                            trans('userinterface.titles.LIST_OR_SAL_BY_SUPP_DET')]))
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_SALES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_INVS_SAL_BY_SUPP'),
                                                            [\Config::get('scsiie.DOC_CAT.SALES'),
                                                            \Config::get('scsiie.DOC_CLS.DOCUMENT'),
                                                            \Config::get('scsiie.DOC_TYPE.INVOICE'),
                                                            \Config::get('scwms.DOC_VIEW.NORMAL'),
                                                            \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                            trans('userinterface.titles.LIST_INVS_SAL_BY_SUPP')]))
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_SALES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_INVS_SAL_BY_SUPP_DET'),
                                                            [\Config::get('scsiie.DOC_CAT.SALES'),
                                                            \Config::get('scsiie.DOC_CLS.DOCUMENT'),
                                                            \Config::get('scsiie.DOC_TYPE.INVOICE'),
                                                            \Config::get('scwms.DOC_VIEW.DETAIL'),
                                                            \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                            trans('userinterface.titles.LIST_INVS_SAL_BY_SUPP_DET')]))
                )
                ->submenu(
                    Link::to('#', trans('actions.DEVOLVE').'<span class="glyphicon glyphicon-triangle-right btn-xs"></span>')
                        ->addClass('test'),
                    \Menu::new()
                        ->addParentClass('dropdown-submenu')
                        ->addClass('dropdown-menu')
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_SALES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_CN_SAL_BY_SUPP'),
                                                            [\Config::get('scsiie.DOC_CAT.SALES'),
                                                            \Config::get('scsiie.DOC_CLS.ADJUST'),
                                                            \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE'),
                                                            \Config::get('scwms.DOC_VIEW.NORMAL'),
                                                            \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                            trans('userinterface.titles.LIST_CN_SAL_BY_SUPP')]))
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_SALES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_CN_SAL_BY_SUPP_DET'),
                                                            [\Config::get('scsiie.DOC_CAT.SALES'),
                                                            \Config::get('scsiie.DOC_CLS.ADJUST'),
                                                            \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE'),
                                                            \Config::get('scwms.DOC_VIEW.DETAIL'),
                                                            \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                            trans('userinterface.titles.LIST_CN_SAL_BY_SUPP_DET')]))
                )
                ->submenu(
                    Link::to('#', 'Surtido'.'<span class="glyphicon glyphicon-triangle-right btn-xs"></span>')
                        ->addClass('test'),
                    \Menu::new()
                        ->addParentClass('dropdown-submenu')
                        ->addClass('dropdown-menu')
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_SALES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_OR_SAL_SUPP'),
                                                            [\Config::get('scsiie.DOC_CAT.SALES'),
                                                            \Config::get('scsiie.DOC_CLS.ORDER'),
                                                            \Config::get('scsiie.DOC_TYPE.ORDER'),
                                                            \Config::get('scwms.DOC_VIEW.NORMAL'),
                                                            \Config::get('scwms.DOC_VIEW_S.SUPP'),
                                                            trans('userinterface.titles.LIST_OR_SAL_SUPP')
                                                          ]))
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_SALES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_OR_SAL_SUPP_DET'),
                                                          [\Config::get('scsiie.DOC_CAT.SALES'),
                                                          \Config::get('scsiie.DOC_CLS.ORDER'),
                                                          \Config::get('scsiie.DOC_TYPE.ORDER'),
                                                          \Config::get('scwms.DOC_VIEW.DETAIL'),
                                                          \Config::get('scwms.DOC_VIEW_S.SUPP'),
                                                          trans('userinterface.titles.LIST_OR_SAL_SUPP_DET')
                                                        ]))
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_SALES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_INVS_SAL_SUPP'),
                                                            [\Config::get('scsiie.DOC_CAT.SALES'),
                                                            \Config::get('scsiie.DOC_CLS.DOCUMENT'),
                                                            \Config::get('scsiie.DOC_TYPE.INVOICE'),
                                                            \Config::get('scwms.DOC_VIEW.NORMAL'),
                                                            \Config::get('scwms.DOC_VIEW_S.SUPP'),
                                                            trans('userinterface.titles.LIST_INVS_SAL_SUPP')
                                                          ]))
                        ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.WHS_SALES')),
                                Link::toRoute('wms.docs.index', trans('userinterface.titles.LIST_INVS_SAL_SUPP_DET'),
                                                          [\Config::get('scsiie.DOC_CAT.SALES'),
                                                          \Config::get('scsiie.DOC_CLS.DOCUMENT'),
                                                          \Config::get('scsiie.DOC_TYPE.INVOICE'),
                                                          \Config::get('scwms.DOC_VIEW.DETAIL'),
                                                          \Config::get('scwms.DOC_VIEW_S.SUPP'),
                                                          trans('userinterface.titles.LIST_INVS_SAL_SUPP_DET')
                                                        ]))
                )
        )
        ->submenu(
            Link::to('#', trans('wms.STOCK_QUERY').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->route('wms.stock.index', trans('wms.ITEM_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_ITEM'), trans('wms.ITEM_STK')])
                ->route('wms.stock.index', trans('wms.PALLET_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_PALLET'), trans('wms.PALLET_STK')])
                ->route('wms.stock.index', trans('wms.LOT_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_LOT'), trans('wms.LOT_STK')])
                ->addIf(session('location_enabled'), Link::toRoute('wms.stock.index', trans('wms.LOC_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_LOCATION'), trans('wms.LOC_STK')]))
                ->route('wms.stock.index', trans('wms.WHS_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_WAREHOUSE'), trans('wms.WHS_STK')])
                ->route('wms.stock.index', trans('wms.BRANCH_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_BRANCH'), trans('wms.BRANCH_STK')])
                ->route('wms.stock.index', trans('wms.LOT_WHS_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE'), trans('wms.LOT_WHS_STK')])
                ->route('wms.stock.index', trans('wms.PALLET_LOT_STK'), [\Config::get('scwms.STOCK_TYPE.STK_BY_PALLET_BY_LOT'), trans('wms.PALLET_LOT_STK')])
                ->route('wms.stock.index', trans('wms.GENERAL_STK'), [\Config::get('scwms.STOCK_TYPE.STK_GENERAL'), trans('wms.GENERAL_STK')])
        )
        ->wrap('div.collapse.navbar-collapse')
        ->setActiveFromRequest();
    });
  }
}
