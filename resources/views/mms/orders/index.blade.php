@extends('templates.basic_form')

@section('head')
  @include('templates.head.shead')
@endsection

@section('title', $sTitle)

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('titlepanel', $sTitle)

@section('content')

  <?php $sRoute="mms.orders"?>

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
    <table id="planes_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
        <thead>
            <tr class="titlerow">
                <th data-priority="1" style="text-align: center;">Folio</th>
                <th data-priority="1" style="text-align: center;">Plan de producción</th>
                <th data-priority="1" style="text-align: center;">Sucursal</th>
                <th data-priority="1">Planta</th>
                <th data-priority="1">Tipo de orden</th>
                <th data-priority="1">Estatus de orden</th>
                <th data-priority="1" style="text-align: center;">Item</th>
                <th data-priority="1" style="text-align: center;">Unidad</th>
                <th data-priority="1" style="text-align: center;">Formula</th>
                <th data-priority="1" style="text-align: center;">Fecha</th>
                <th data-priority="1" style="text-align: center;">Cargas</th>
                <th data-priority="1" style="text-align: center;">Orden Padre</th>
                <th style="text-align: center;">Estatus</th>
                <th style="text-align: center;">Opciones</th>
            </tr>
        </thead>
        <tbody>
          @foreach ($orders as $orden)
            <tr>
                <td>{{ session('utils')->formatFolio($orden->folio) }}</td>
                <td>{{ session('utils')->formatFolio($orden->plan->folio) }}</td>
                <td>{{ $orden->branch->name }}</td>
                <td>{{ $orden->floor->name }}</td>
                <td>{{ $orden->type->name }}</td>
                <td>{{ $orden->status->name }}</td>
                <td>{{ $orden->item->name }}</td>
                <td>{{ $orden->unit->code }}</td>
                <td>{{ $orden->formula->identifier }}</td>
                <td>{{ $orden->date }}</td>
                <td>{{ $orden->charges }}</td>
                <td>{{ $orden->father_order }}</td>
                <td>
      						@if (! $orden->is_deleted)
      								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
      						@else
      								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
      						@endif
      					</td>
                <td style="text-align: center;">
      						<?php
      								$oRegistry = $orden;
      								$iRegistryId = $orden->id_order;
      								$loptions = [
      									\Config::get('scsys.OPTIONS.EDIT'),
      								];
      						?>
      						@include('templates.list.options')
      					</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  </div>
@endsection

@section('js')
  <script src="{{ asset('js/mms/planes/tables.js')}}"></script>
@endsection

@section('footer')
    @include('templates.footer')
@endsection
