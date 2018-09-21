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
    {!! Form::button(trans('actions.CONSUME'),
                      ['class' => 'btn btn-warning',
                       'onClick' => 'consumeByButton()']) !!}
  @endsection

  @include('mms.orders.kardex')
  @include('mms.orders.consumptions')
  @include('mms.orders.orderview')

  <div class="row">
    <table id="orders_table" class="table table-striped table-bordered display" cellspacing="0" width="100%">
        <thead>
            <tr class="titlerow">
                <th>id_order</th>
                <th data-priority="1" style="text-align: center;">Folio</th>
                <th data-priority="1" style="text-align: center;">Fecha</th>
                <th data-priority="1">Identificador</th>
                <th data-priority="1">Plan de producción</th>
                <th>Planta</th>
                <th>Tipo de order</th>
                <th data-priority="1">-</th>
                <th data-priority="1">Estatus de order</th>
                <th style="text-align: center;">Opciones</th>
                <th data-priority="1" style="text-align: center;">Cve Item</th>
                <th data-priority="1" style="text-align: center;">Item</th>
                <th data-priority="1" style="text-align: center;">Un.</th>
                <th data-priority="1" style="text-align: center;">Formula</th>
                <th data-priority="1" style="text-align: right;">Cant.</th>
                <th style="text-align: center;">Sucursal</th>
                <th style="text-align: center;">order Padre</th>
                <th style="text-align: center;">Estatus</th>
                <th>Creado</th>
                <th>Usuario</th>
                <th>Modificado</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
          @foreach ($orders as $order)
            <tr>
                <td>{{ $order->id_order }}</td>
                <td>{{ session('utils')->formatFolio($order->folio) }}</td>
                <td>{{ $order->date }}</td>
                <td>{{ $order->identifier }}</td>
                <td>{{ session('utils')->formatFolio($order->plan_folio).'-'.$order->production_plan }}</td>
                <td>{{ $order->floor_name }}</td>
                <td>{{ $order->type_name }}</td>
                <td><a class="{{ App\SUtils\SGuiUtils::getClassOfPOStatus($order->status_id) }}">
                      <i class="glyphicon glyphicon-certificate"></i>
                    </a>
                </td>
                <td>{{ $order->status_name }}</td>
                <td style="text-align: center;">
                  @include('mms.orders.previous')
                  @include('mms.orders.next')
                  @include('mms.orders.kardexbtn')
                  <?php
                    $oRegistry = $order;
                    $iRegistryId = $order->id_order;

                    if ($order->status_id == \Config::get('scmms.PO_STATUS.ST_NEW')) {
                      $loptions = [
                        \Config::get('scsys.OPTIONS.EDIT'),
                        \Config::get('scsys.OPTIONS.DESTROY'),
                        \Config::get('scsys.OPTIONS.ACTIVATE'),
                      ];
                    }
                    else {
                      $loptions = [];
                    }
                  ?>
                  @include('mms.orders.printbtn')
                  @include('mms.orders.seeorder')
                  @include('templates.list.options')
              </td>
              <td>{{ $order->item_code }}</td>
              <td>{{ $order->item }}</td>
              <td>{{ $order->unit_code }}</td>
              <td>{{ $order->form_identifier.'-V'.$order->form_version }}</td>
              <td style="text-align: right;">{{ $order->charges }}</td>
              <td>{{ $order->branch_name }}</td>
              @if ($order->father_order_id == '1')
                <td>{{ 'NA' }}</td>
              @else
                <td>{{ 'OP-'.session('utils')->formatFolio($order->father_folio) }}</td>
              @endif
              <td>
    						@if (! $order->is_deleted)
    								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
    						@else
    								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
    						@endif
    					</td>
              <td>{{ $order->created_at }}</td>
              <td>{{ $order->creation_user_name }}</td>
              <td>{{ $order->updated_at }}</td>
              <td>{{ $order->mod_user_name }}</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  </div>
@endsection


@section('js')
  <script src="{{ asset('js/mms/orders/tables.js')}}"></script>
  <script src="{{ asset('js/mms/orders/SOrdersCore.js')}}"></script>
  @include('mms.orders.jssection')
  <script src="{{ asset('js/mms/orders/SKardexCore.js')}}"></script>
  <script src="{{ asset('js/mms/orders/SGuiKardex.js')}}"></script>
  <script src="{{ asset('js/mms/orders/SChargesCore.js')}}"></script>
  <script src="{{ asset('js/mms/orders/SGuiCharges.js')}}"></script>
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
