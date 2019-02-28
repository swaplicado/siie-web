@if (\Route::has($sRoute.'.changesuperpass'))
  <li><a href="{{ route($sRoute.'.changesuperpass', $v_id) }}">
        <i class="glyphicon glyphicon-lock btn-sm"></i>&nbsp;{{ "Reestablecer contraseña" }}
      </a>
  </li>
@endif
