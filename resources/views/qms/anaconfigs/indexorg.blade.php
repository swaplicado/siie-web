@extends('templates.basic_form')

@section('head')
  @include('templates.head.shead')
@endsection

@section('title', trans('qms.ORG_VS_ITEMS'))

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('titlepanel', trans('qms.ORG_VS_ITEMS'))

@section('content')

  <?php $sRoute="qms.anaconfigs"?>

  @section('filters')
    {!! Form::open(['route' => $sRoute.'.index',
      'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
      <div class="form-group">
        <div class="input-group">
          @include('templates.list.search')
          <span class="input-group-btn">
            <button id="searchbtn" type="submit" class="form-control">
              <span class="glyphicon glyphicon-search"></span>
            </button>
          </span>
        </div>
      </div>
      {!! Form::close() !!}
    @endsection

  @section('create')
    <a href={{  route($sRoute.'.create', \Config::get('scqms.ANALYSIS_TYPE.OL')) }} class="btn btn-success btn-min"
      style="visibility: {{ App\SUtils\SValidation::isRendered(\Config::get('scsys.OPERATION.CREATE'), $actualUserPermission, 0) }};">
      {{ trans('actions.CREATE') }}
    </a>
  @endsection
  <div class="row">
    <table id="anaconfigs_org_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
        <thead>
            <tr class="titlerow">
                <th data-priority="1">Análisis</th>
                <th data-priority="1">T. A.</th>
                <th data-priority="1">Tipo Conf.</th>
                <th data-priority="1">Referencia</th>
                <th data-priority="1">Especificación</th>
                <th data-priority="1">Resultado</th>
                <th data-priority="1" style="text-align: center;">Estatus</th>
                <th style="text-align: center;">Opciones</th>
                <th>Creado</th>
                <th>Usuario</th>
                <th>Modificado</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
          @foreach ($lConfigs as $anaconfig)
            <tr>
                <td>{{ $anaconfig->_analysis }}</td>
                <td>{{ $anaconfig->_ana_type }}</td>
                <td>{{ $anaconfig->_ntype }}</td>
                <td>{{ $anaconfig->_nname }}</td>
                <td>{{ $anaconfig->specification }}</td>
                <td>{{ $anaconfig->result }}</td>
                <td style="text-align: center;">
                    @if (! $anaconfig->is_deleted)
                        <span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
                    @else
                        <span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    <?php
                        $oRegistry = $anaconfig;
                        $iRegistryId = $anaconfig->id_config;
                        $loptions = [
                            \Config::get('scsys.OPTIONS.EDIT'),
                            \Config::get('scsys.OPTIONS.DESTROY'),
                            \Config::get('scsys.OPTIONS.ACTIVATE'),
                            \Config::get('scsys.OPTIONS.COPY'),
                        ];
                    ?>
                    @include('templates.list.options')
                </td>
                <td>{{ $anaconfig->created_at }}</td>
                <td>{{ $anaconfig->creation_user_name }}</td>
                <td>{{ $anaconfig->updated_at }}</td>
                <td>{{ $anaconfig->mod_user_name }}</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  </div>
@endsection

@section('js')
  <script src="{{ asset('js/qms/tables.js')}}"></script>
@endsection

@section('footer')
    @include('templates.footer')
@endsection
