@extends('templates.basic_form')

@section('head')
  @include('templates.head.shead')
@endsection

@section('title', trans('qms.ANALYSIS'))

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('titlepanel', trans('qms.ANALYSIS'))

@section('content')

  <?php $sRoute="qms.analysis"?>

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
    @include('templates.form.create')
  @endsection
  <div class="row">
    <table id="analysiss_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
        <thead>
            <tr class="titlerow">
                <th data-priority="1">Código</th>
                <th data-priority="1">Análisis</th>
                <th data-priority="1">Tipo de análisis</th>
                <th data-priority="1" style="text-align: center;">Estatus</th>
                <th style="text-align: center;">Opciones</th>
                <th>Creado</th>
                <th>Usuario</th>
                <th>Modificado</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
          @foreach ($lAnalysis as $analysis)
            <tr>
                <td>{{ $analysis->code }}</td>
                <td>{{ $analysis->name }}</td>
                <td>{{ $analysis->type_code }}</td>
                <td style="text-align: center;">
                    @if (! $analysis->is_deleted)
                        <span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
                    @else
                        <span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
                    @endif
                </td>
                <td style="text-align: center;">
                    <?php
                        $oRegistry = $analysis;
                        $iRegistryId = $analysis->id_analysis;
                        $loptions = [
                            \Config::get('scsys.OPTIONS.EDIT'),
                            \Config::get('scsys.OPTIONS.DESTROY'),
                            \Config::get('scsys.OPTIONS.ACTIVATE'),
                            \Config::get('scsys.OPTIONS.COPY'),
                        ];
                    ?>
                    @include('templates.list.options')
                </td>
                <td>{{ $analysis->created_at }}</td>
                <td>{{ $analysis->creation_user_name }}</td>
                <td>{{ $analysis->updated_at }}</td>
                <td>{{ $analysis->mod_user_name }}</td>
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
