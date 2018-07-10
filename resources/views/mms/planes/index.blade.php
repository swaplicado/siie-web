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

  <?php $sRoute="mms.planes"?>

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
                <th data-priority="1" style="text-align: center;">Fecha inicio</th>
                <th data-priority="1" style="text-align: center;">Fecha fin</th>
                <th data-priority="1">Nombre</th>
                <th data-priority="1">Planta</th>
                <th data-priority="1">Sucursal</th>
                <th data-priority="1" style="text-align: center;">Estatus</th>
                <th style="text-align: center;">Opciones</th>
            </tr>
        </thead>
        <tbody>
          @foreach ($planes as $plan)
            <tr>
                <td>{{ str_pad($plan->folio, 5, "0", STR_PAD_LEFT) }}</td>
                <td>{{ $plan->dt_start }}</td>
                <td>{{ $plan->dt_end }}</td>
                <td>{{ $plan->production_plan }}</td>
                <td>{{ $plan->plant }}</td>
                <td>{{ $plan->branch }}</td>
                <td>
      						@if (! $plan->is_deleted)
      								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
      						@else
      								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
      						@endif
      					</td>
                <td style="text-align: center;">
      						<?php
      								$oRegistry = $plan;
      								$iRegistryId = $plan->id_production_plan;
      								$loptions = [
      									\Config::get('scsys.OPTIONS.EDIT'),
      									\Config::get('scsys.OPTIONS.DESTROY'),
      									\Config::get('scsys.OPTIONS.ACTIVATE'),
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
	<script>
			 var iFolio = <?php echo json_encode($iFolio); ?>;

			 if (iFolio != 0) {
						swal(
								'Folio: ' + iFolio,
								'',
								'success'
							);
			 }
	</script>
@endsection

@section('footer')
    @include('templates.footer')
@endsection
