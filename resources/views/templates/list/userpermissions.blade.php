@if (App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.CREATE'), $actualUserPermission, $v_created_by))
  <li>
    <a href="{{ route('admin.userpermissions.creator',  $user->username . ",,". $user->id) }}" title="{{ trans('userinterface.tooltips.USERPERMISSION') }}">
      <i class="glyphicon glyphicon-eye-open btn-sm"></i>&nbsp;{{ trans('userinterface.buttons.USERPERMISSION') }}
    </a>
  </li>
@endif
