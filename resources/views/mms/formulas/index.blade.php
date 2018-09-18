@extends('templates.basic_form')

@section('head')
  @include('templates.head.shead')
@endsection

@section('title', trans('mms.FORMULAS'))

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('titlepanel', trans('mms.FORMULAS'))

@section('content')

  <?php $sRoute="mms.formulas"?>

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
    <table id="formulas_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
        <thead>
            <tr class="titlerow">
                <th data-priority="1">Identificador</th>
                <th data-priority="1" style="text-align: center;">Versión</th>
                <th data-priority="1" style="text-align: center;">Fecha</th>
                <th data-priority="1" style="text-align: center;">Código</th>
                <th data-priority="1">Material/producto</th>
                <th data-priority="1" style="text-align: center;">Un</th>
                <th data-priority="1" style="text-align: center;">Estatus</th>
                <th style="text-align: center;">Opciones</th>
                <th>Creado</th>
                <th>Usuario</th>
                <th>Modificado</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
          @foreach ($formulas as $formula)
            <tr>
                <td>{{ $formula->identifier }}</td>
                <td style="text-align: center;">{{ $formula->version }}</td>
                <td style="text-align: center;">{{ $formula->dt_date }}</td>
                <td style="text-align: center;">{{ $formula->item_code }}</td>
                <td>{{ $formula->item }}</td>
                <td style="text-align: center;">{{ $formula->unit_code }}</td>
                <td style="text-align: center;">
      						@if (! $formula->is_deleted)
      								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
      						@else
      								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
      						@endif
      					</td>
                <td style="text-align: center;">
      						<?php
      								$oRegistry = $formula;
      								$iRegistryId = $formula->id_formula;
      								$loptions = [
      									\Config::get('scsys.OPTIONS.EDIT'),
      									\Config::get('scsys.OPTIONS.DESTROY'),
      									\Config::get('scsys.OPTIONS.ACTIVATE'),
      									\Config::get('scsys.OPTIONS.COPY'),
      								];
      						?>
      						@include('templates.list.options')
                  <a href="{{ route('mms.formulas.create', [$formula->id_formula]) }}" title="Nueva Versión"
                    class="btn btn-primary btn-xs">
                    <span class="glyphicon glyphicon-expand" aria-hidden = "true"/>
                  </a>
                  <a href="{{ route('mms.formulas.print', [$formula->id_formula]) }}"
                    title="Imprimir" target="_blank"
                    class="btn btn-default btn-xs">
                    <span class="glyphicon glyphicon-print" aria-hidden = "true"/>
                  </a>
      					</td>
                <td>{{ $formula->created_at }}</td>
                <td>{{ $formula->creation_user_name }}</td>
                <td>{{ $formula->updated_at }}</td>
                <td>{{ $formula->mod_user_name }}</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  </div>
@endsection

@section('js')
  <script src="{{ asset('js/formulas/table.js')}}"></script>
@endsection

@section('footer')
    @include('templates.footer')
@endsection
