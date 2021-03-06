<?php namespace App\Menu;

use Spatie\Menu\Laravel\Link;

class STmsMenu {

  public static function createMenu()
  {
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
  }
}
