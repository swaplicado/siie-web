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
            Link::to('#', trans('siie.ITEMS').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->route('siie.families.index', trans('siie.FAMILIES'))
                ->route('siie.groups.index', trans('siie.GROUPS'))
                ->submenu(
                    Link::to('#', trans('siie.GENDERS').'<span class="glyphicon glyphicon-triangle-right btn-xs"></span>')
                        ->addClass('test'),
                    \Menu::new()
                        ->addParentClass('dropdown-submenu')
                        ->addClass('dropdown-menu')
                        ->route('siie.genders.index', trans('siie.MATERIALS'), [\Config::get('scsiie.ITEM_CLS.MATERIAL')])
                        ->route('siie.genders.index', trans('siie.PRODUCTS'), [\Config::get('scsiie.ITEM_CLS.PRODUCT')])
                        ->route('siie.genders.index', trans('siie.SPENDING'), [\Config::get('scsiie.ITEM_CLS.SPENDING')])
                )
                ->route('siie.items.index', trans('siie.MATERIALS'), [\Config::get('scsiie.ITEM_CLS.MATERIAL')])
                ->route('siie.items.index', trans('siie.PRODUCTS'), [\Config::get('scsiie.ITEM_CLS.PRODUCT')])
                ->route('siie.items.index', trans('siie.SPENDING'), [\Config::get('scsiie.ITEM_CLS.SPENDING')])
                ->submenu(
                    Link::to('#', trans('siie.UNITS').'<span class="glyphicon glyphicon-triangle-right btn-xs"></span>')
                        ->addClass('test'),
                    \Menu::new()
                        ->addParentClass('dropdown-submenu')
                        ->addClass('dropdown-menu')
                        ->route('siie.units.index', trans('siie.UNITS'))
                        ->route('siie.units.index', trans('siie.CONVERTIONS'))
                )

        )
        ->submenu(
            Link::to('#', trans('siie.CONFIGURATION').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->route('siie.years.index', trans('siie.ACG_YEAR_PER'))
                ->html('', ['role' => 'separator', 'class' => 'divider'])
                ->route('siie.bps.index', trans('siie.BPS'))
                ->route('siie.branches.index', trans('siie.BRANCHES'))
        )
        ->submenu(
            Link::to('#', trans('siie.DOCUMENTS').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
                ->addClass('dropdown-menu')
                ->submenu(
                    Link::to('#', trans('siie.DOCS_PUR').'<span class="glyphicon glyphicon-triangle-right btn-xs"></span>')
                        ->addClass('test'),
                    \Menu::new()
                        ->addParentClass('dropdown-submenu')
                        ->addClass('dropdown-menu')
                        ->route('siie.docs.index', trans('userinterface.titles.LIST_PUR_ORD'),
                                                    [\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                    \Config::get('scsiie.DOC_CLS.ORDER'),
                                                    \Config::get('scsiie.DOC_TYPE.ORDER'),
                                                    trans('userinterface.titles.LIST_PUR_ORD')])
                        ->route('siie.docs.index', trans('userinterface.titles.LIST_PUR_INV'),
                                                    [\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                    \Config::get('scsiie.DOC_CLS.DOCUMENT'),
                                                    \Config::get('scsiie.DOC_TYPE.INVOICE'),
                                                    trans('userinterface.titles.LIST_PUR_INV')])
                        ->route('siie.docs.index', trans('userinterface.titles.LIST_PUR_CN'),
                                                    [\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                    \Config::get('scsiie.DOC_CLS.ADJUST'),
                                                    \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE'),
                                                    trans('userinterface.titles.LIST_PUR_CN')])
                )
                ->submenu(
                    Link::to('#', trans('siie.DOCS_SAL').'<span class="glyphicon glyphicon-triangle-right btn-xs"></span>')
                        ->addClass('test'),
                    \Menu::new()
                        ->addParentClass('dropdown-submenu')
                        ->addClass('dropdown-menu')
                        ->route('siie.docs.index', trans('userinterface.titles.LIST_SAL_ORD'),
                                                    [\Config::get('scsiie.DOC_CAT.SALES'),
                                                    \Config::get('scsiie.DOC_CLS.ORDER'),
                                                    \Config::get('scsiie.DOC_TYPE.ORDER'),
                                                    trans('userinterface.titles.LIST_SAL_ORD')])
                        ->route('siie.docs.index', trans('userinterface.titles.LIST_SAL_INV'),
                                                    [\Config::get('scsiie.DOC_CAT.SALES'),
                                                    \Config::get('scsiie.DOC_CLS.DOCUMENT'),
                                                    \Config::get('scsiie.DOC_TYPE.INVOICE'),
                                                    trans('userinterface.titles.LIST_SAL_INV')])
                        ->route('siie.docs.index', trans('userinterface.titles.LIST_SAL_CN'),
                                                    [\Config::get('scsiie.DOC_CAT.SALES'),
                                                    \Config::get('scsiie.DOC_CLS.ADJUST'),
                                                    \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE'),
                                                    trans('userinterface.titles.LIST_SAL_CN')])
                )
        )
        ->wrap('div.collapse.navbar-collapse')
        ->setActiveFromRequest();
    });
  }
}
