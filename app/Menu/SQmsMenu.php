<?php namespace App\Menu;

use Spatie\Menu\Laravel\Link;
use App\SUtils\SValidation;

class SQmsMenu {

  public static function createMenu()
  {
    \Menu::macro('main', function () {
      return \Menu::new()
        ->addClass('nav navbar-nav')
        ->link('', '')
        ->route('qms.home', trans('qms.MODULE'))
        ->submenu(
          Link::to('#', trans('qms.QLTY_DOCS').'<span class="caret"></span>')
              ->addClass('dropdown-toggle')
              ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
          \Menu::new()
            ->addClass('dropdown-menu')
            ->submenu(
                  Link::to('#', trans('qms.ANALYSIS_CONFIG').'<span class="glyphicon glyphicon-triangle-right btn-xs"></span>')
                      ->addClass('test'),
                  \Menu::new()
                    ->addParentClass('dropdown-submenu')
                    ->addClass('dropdown-menu')
                    ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.QMS_CONFIG_DOCS')),
                            Link::toRoute('qms.configdocs.indext', trans('qms.CFG_DOCS').' FQ', [\Config::get('scqms.CFG_ZONE.FQ')]))
                    ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.QMS_CONFIG_DOCS')),
                            Link::toRoute('qms.configdocs.indext', trans('qms.CFG_DOCS').' MB', [\Config::get('scqms.CFG_ZONE.MB')]))
                    ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.QMS_ANALYSIS')),
                            Link::toRoute('qms.analysis.index', trans('qms.ANALYSIS')))
                    ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.QMS_CERTIFICATES')),
                            Link::toRoute('qms.certconfigs.index', trans('qms.ANALYSIS_VS_ITEMS').' (Certificados)', [0]))
                    ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.QMS_CERTIFICATES')),
                            Link::toRoute('qms.certconfigs.index', trans('qms.ORG_VS_ITEMS'), [\Config::get('scqms.ANALYSIS_TYPE.OL')]))
                    ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.ERP_SIGNATURE_AUTHORIZATION')),
                            Link::toRoute('siie.signauths.index', trans('siie.SIGNATURE_AUTHORIZATIONS')))
                    )
            
            ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.QMS_DOCUMENTS')),
                      Link::toRoute('siie.pos.index', trans('qms.LOTS_AND_POS'), [\Config::get('scsiie.OP_FROM.QUALITY')]))
            ->addIf(SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'), \Config::get('scperm.PERMISSION.QMS_DOCUMENTS')),
                        Link::toRoute('qms.qdocs.docs', trans('qms.QLTY_DOCS')))
          )
        ->submenu(
            Link::to('#', trans('qms.STOCK_QUALITY').'<span class="caret"></span>')
                ->addClass('dropdown-toggle')
                ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
            \Menu::new()
            ->addClass('dropdown-menu')
          ->route('qms.segregations.index', trans('qms.VIEW_INSPECTION'),
                                          [
                                            trans('qms.VIEW_INSPECTION'),
                                            \Config::get('scqms.SEGREGATION_TYPE.INSPECTED'),
                                            \Config::get('scqms.QMS_VIEW.INSPECTIONCLASSIFY'),
                                            \Config::get('scqms.TYPE_VIEW.BY_LOT')
                                          ])
          ->route('qms.segregations.index', trans('qms.VIEW_QUARENTINE'),
                                          [
                                            trans('qms.VIEW_QUARENTINE'),
                                            \Config::get('scqms.SEGREGATION_TYPE.QUARANTINE'),
                                            \Config::get('scqms.QMS_VIEW.QUARANTINECLASSIFY'),
                                            \Config::get('scqms.TYPE_VIEW.BY_LOT')
                                          ])
          ->route('qms.segregations.index', trans('qms.VIEW_INS_PALLET'),
                                          [
                                            trans('qms.VIEW_INS_PALLET'),
                                            \Config::get('scqms.SEGREGATION_TYPE.INSPECTED'),
                                            \Config::get('scqms.QMS_VIEW.INSPECTIONCLASSIFY'),
                                            \Config::get('scqms.TYPE_VIEW.BY_PALLET')
                                          ])
          ->route('qms.segregations.index', trans('qms.VIEW_QUA_PALLET'),
                                          [
                                            trans('qms.VIEW_QUA_PALLET'),
                                            \Config::get('scqms.SEGREGATION_TYPE.QUARANTINE'),
                                            \Config::get('scqms.QMS_VIEW.QUARANTINECLASSIFY'),
                                            \Config::get('scqms.TYPE_VIEW.BY_PALLET')
                                          ])
          ->route('qms.segregations.index', trans('qms.VIEW_INS_LOT'),
                                          [
                                            trans('qms.VIEW_INS_LOT'),
                                            \Config::get('scqms.SEGREGATION_TYPE.INSPECTED'),
                                            \Config::get('scqms.QMS_VIEW.INSPECTIONCLASSIFY'),
                                            \Config::get('scqms.TYPE_VIEW.BY_ONLY_LOT')
                                          ])
          ->route('qms.segregations.index', trans('qms.VIEW_QUA_LOT'),
                                          [
                                            trans('qms.VIEW_QUA_LOT'),
                                            \Config::get('scqms.SEGREGATION_TYPE.QUARANTINE'),
                                            \Config::get('scqms.QMS_VIEW.QUARANTINECLASSIFY'),
                                            \Config::get('scqms.TYPE_VIEW.BY_ONLY_LOT')
                                          ])

      )

      ->submenu(
          Link::to('#', trans('qms.STOCK_QUALITY_CB').'<span class="caret"></span>')
              ->addClass('dropdown-toggle')
              ->setAttributes(['data-toggle' => 'dropdown', 'role' => 'button']),
          \Menu::new()
          ->addClass('dropdown-menu')

          ->route('qms.segregations.consult', trans('qms.VIEW_INS_QUA'),[
                                  trans('qms.VIEW_INS_QUA'),
                                  '1'
                                ])
          ->route('qms.segregations.consult', trans('qms.VIEW_REL'),[
                                  trans('qms.VIEW_REL'),
                                  '2'
                                ])
          ->route('qms.segregations.consult', trans('qms.VIEW_REF'),[
                                  trans('qms.VIEW_REF'),
                                  '3'
                                ])
      )
        ->route('qms.segregations.binnacle', trans('qms.BINNACLE'))
        ->route('qms.segregations.segregatePalletsIndex', trans('qms.VIEW_SEGREGATE_PALLET'))
        ->wrap('div.collapse.navbar-collapse')
        ->setActiveFromRequest();
    });
  }
}
