@if (App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.SUPER'), $actualUserPermission, $v_created_by))
  <li><a href="{{ route('admin.usraccess.edit', $v_id) }}">
        <i class="glyphicon glyphicon-lock btn-sm"></i>&nbsp;{{ trans('userinterface.buttons.ACCESS') }}
      </a>
  </li>
@endif
