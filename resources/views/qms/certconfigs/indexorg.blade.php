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

  <?php $sRoute="qms.certconfigs"?>

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
          @foreach ($lConfigs as $certconfig)
            <tr>
                <td>{{ $certconfig->_analysis }}</td>
                <td>{{ $certconfig->_ana_type }}</td>
                <td>{{ $certconfig->_ntype }}</td>
                <td>{{ $certconfig->_nname }}</td>
                <td>{{ $certconfig->specification }}</td>
                <td>{{ $certconfig->result }}</td>
                <td style="text-align: center;">
                    @if (! $certconfig->is_deleted)
                        <span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
                    @else
                        <span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    <?php
                        $oRegistry = $certconfig;
                        $iRegistryId = $certconfig->id_cert_configuration;
                        $loptions = [
                            \Config::get('scsys.OPTIONS.EDIT'),
                            \Config::get('scsys.OPTIONS.DESTROY'),
                            \Config::get('scsys.OPTIONS.ACTIVATE'),
                            \Config::get('scsys.OPTIONS.COPY'),
                        ];
                    ?>
                    @include('templates.list.options')
                </td>
                <td>{{ $certconfig->created_at }}</td>
                <td>{{ $certconfig->creation_user_name }}</td>
                <td>{{ $certconfig->updated_at }}</td>
                <td>{{ $certconfig->mod_user_name }}</td>
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
