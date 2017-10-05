@if ($oRegistry->is_deleted == \Config::get('scsys.STATUS.DEL')
              && App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.DEL'), $actualUserPermission, $v_created_by))
  <li>
    <a href="{{ route($sRoute.'.activate', $v_id) }}">
      <i class="glyphicon glyphicon-ok-sign"></i>
      &nbsp;{{ trans('userinterface.buttons.ACTIVATE') }}
    </a>
  </li>
@endif
