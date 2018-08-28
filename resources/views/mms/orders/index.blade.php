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
						{!! Form::select('po_status', $lOrderStatus, $iOrderStatus,
															['class'=>'form-control']) !!}
					</span>
          <span class="input-group-btn">
            {!! Form::text('filterDate', $sFilterDate, ['class' => 'form-control', 'id' => 'filterDate']); !!}
          </span>
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

  @include('mms.orders.kardex')

  <div class="row">
    <table id="orders_table" class="table table-striped table-bordered display" cellspacing="0" width="100%">
        <thead>
            <tr class="titlerow">
                <th data-priority="1" style="text-align: center;">Folio</th>
                <th data-priority="1" style="text-align: center;">Fecha</th>
                <th data-priority="1">Identificador</th>
                <th data-priority="1">Plan de producción</th>
                <th>Planta</th>
                <th>Tipo de orden</th>
                <th data-priority="1">Estatus de orden</th>
                <th data-priority="1" style="text-align: center;">Item</th>
                <th data-priority="1" style="text-align: center;">Un.</th>
                <th data-priority="1" style="text-align: center;">Formula</th>
                <th data-priority="1" style="text-align: center;">Cargas</th>
                <th style="text-align: center;">Opciones</th>
                <th style="text-align: center;">Sucursal</th>
                <th style="text-align: center;">Orden Padre</th>
                <th style="text-align: center;">Estatus</th>
            </tr>
        </thead>
        <tbody>
          @foreach ($orders as $orden)
            <tr>
                <td>{{ session('utils')->formatFolio($orden->folio) }}</td>
                <td>{{ $orden->date }}</td>
                <td>{{ $orden->identifier }}</td>
                <td>{{ session('utils')->formatFolio($orden->plan->folio).'-'.$orden->plan->production_plan }}</td>
                <td>{{ $orden->floor->name }}</td>
                <td>{{ $orden->type->name }}</td>
                <td>{{ $orden->status->name }}</td>
                <td>{{ $orden->item->name }}</td>
                <td>{{ $orden->unit->code }}</td>
                <td>{{ $orden->formula->identifier.'-V'.$orden->formula->version }}</td>
                <td>{{ $orden->charges }}</td>
                <td style="text-align: center;">
                  @include('mms.orders.previous')
                  @include('mms.orders.next')
                  @include('mms.orders.kardexbtn')
                  <?php
                    $oRegistry = $orden;
                    $iRegistryId = $orden->id_order;
                    $loptions = [
                      \Config::get('scsys.OPTIONS.EDIT'),
                      \Config::get('scsys.OPTIONS.DESTROY'),
                      \Config::get('scsys.OPTIONS.ACTIVATE'),
                    ];
                  ?>
                  @include('templates.list.options')
              </td>
              <td>{{ $orden->branch->name }}</td>
              @if ($orden->father_order_id == '1')
                <td>{{ 'NA' }}</td>
              @else
                <td>{{ 'OP-'.session('utils')->formatFolio($orden->father->folio) }}</td>
              @endif
              <td>
    						@if (! $orden->is_deleted)
    								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
    						@else
    								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
    						@endif
    					</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  </div>
@endsection

@include('mms.orders.jssection')
@section('js')
  <script src="{{ asset('js/mms/orders/tables.js')}}"></script>
  <script src="{{ asset('js/mms/orders/SKardexCore.js')}}"></script>
  <script src="{{ asset('js/mms/orders/SGuiKardex.js')}}"></script>
  <script src="{{ asset('moment/moment.js') }}"></script>
	<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>

  <script>
      $(function() {
        $('input[id="filterDate"]').daterangepicker({
          locale: {
                 format: 'DD/MM/YYYY'
             }
        });
      });

      $('#filterDate').on('apply.daterangepicker', function(ev, picker) {
        console.log(picker.startDate.format('YYYY-MM-DD'));
        console.log(picker.endDate.format('YYYY-MM-DD'));
      });
  </script>
@endsection

@section('footer')
    @include('templates.footer')
@endsection
