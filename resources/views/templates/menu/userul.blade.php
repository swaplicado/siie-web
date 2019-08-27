<ul class="nav navbar-nav navbar-right">
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
      {{ Auth::check() ? Auth::user()->username : '' }}
      <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
      @if (\Auth::user()->user_type_id == \Config::get('scsys.TP_USER.ADMIN'))
        <li>
            <a href="{{ route('plantilla.admin') }}">{{ trans('userinterface.ADMINISTRATOR') }}</a>
        </li>
      @endif

      <li><a href="{{ route('start.branchwhs') }}">{{"Sucursal :"}}<?php echo session()->has('branch') ? session('branch')->name : '' ?></a></li>
      <li><a href="{{ route('start.selectwhs') }}">{{"Almacen :"}}<?php echo session()->has('whs') ? session('whs')->name : '' ?></a></li>
      <li><a href="{{ route('manage.users.changepass', [\Auth::user()->id]) }}"><i class="glyphicon glyphicon-user"></i>  Cambiar contraseña</a></li>
      <li><a type='button' data-toggle='modal' data-target='#myModal' href="#"><i class="glyphicon glyphicon-info-sign"></i>  Info</a></li>
      <li><a type='button' data-toggle='modal' data-target='#syncMms' href="#"><i class="glyphicon glyphicon-refresh"></i>  Sincronizar</a></li>
      <li><a href="{{ route('auth.logout') }}">{{ trans('userinterface.EXIT') }}</a></li>

    </ul>
  </li>
</ul>
