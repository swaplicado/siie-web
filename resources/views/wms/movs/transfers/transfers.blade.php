@extends('templates.basic_form')

@section('head')
	@include('templates.head')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $sTitle)
@section('titlepanel', $sTitle)

@section('content')
	<?php $sRoute="wms.movs"?>

      <div class="row">
        <div class="col-md-12">
          <table id="transfers_list_table" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="100%">
              <thead>
                  <tr class="titlerow">
                      <th>id_mov</th>
                      <th>Folio</th>
                      <th>Fecha</th>
                      <th>Sucursal origen</th>
                      <th>Sucursal destino</th>
											<th>Total</th>
											<th>{{ $iClass == \Config::get('scwms.MVT_CLS_IN') ? 'Recibido' : 'Enviado' }}</th>
                      <th>Opciones</th>
                      <th>Enviado por</th>
                  </tr>
              </thead>
              <tbody>
                  @foreach ($lList as $mov)
                      <tr>
                        <td>{{ $mov->id_mvt }}</td>
                        <td style="text-align: center;">{{ $mov->mov_code.'-'.session('utils')->formatFolio($mov->folio) }}</td>
                        <td style="text-align: center;">{{ $mov->dt_date }}</td>
												<td>{{ $mov->src_branch_name }}</td>
												<td>{{ $mov->des_branch_name }}</td>
												<td align="right">{{ session('utils')->formatNumber($mov->total, \Config::get('scsiie.FRMT.QTY')) }}</td>
												<td align="right">{{ session('utils')->formatNumber($mov->indicted, \Config::get('scsiie.FRMT.QTY')) }}</td>
												<td style="text-align: center;">
													<div>
															<a href="{{ route('wms.movs.destroy', $mov->src_id_mvt) }}"
																		style="visibility: {{ App\SUtils\SValidation::isRendered(\Config::get('scsys.OPERATION.DEL'), $actualUserPermission, $mov->created_by_id) }};"
																		class="btn btn-danger btn-xs"
																		title="{{ trans('userinterface.tooltips.DELETE') }}"
																		data-btn-ok-label="{{ trans('messages.options.MSG_YES') }}"
																		data-btn-cancel-label="{{ trans('messages.options.MSG_NO') }}"
																		data-singleton="true" data-title="{{ trans('messages.confirm.MSG_CONFIRM') }}">
																<span class="glyphicon glyphicon-trash" aria-hidden = "true"/>
															</a>
															@if ($mov->src_is_deleted == \Config::get('scsys.STATUS.DEL')
															              && App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.DEL'), $actualUserPermission, $mov->created_by_id))
															  <li>
															    <a href="{{ route('wms.movs.activate', $mov->src_id_mvt) }}" class="btn btn-default btn-xs">
															      <i class="glyphicon glyphicon-ok-sign"></i>
															      &nbsp;{{ trans('userinterface.buttons.ACTIVATE') }}
															    </a>
															  </li>
															@endif
															<a
															href="{{ route('wms.movs.print', $mov->id_mvt) }}"
															title="{{ trans('actions.PRINT') }}"
															target="_blank"
																class="btn btn-primary btn-xs">
																<span class="glyphicon glyphicon-print" aria-hidden = "true"/>
															</a>
													</div>
				      					</td>
												<td>{{ $mov->username }}</td>
                      </tr>
                  @endforeach
              </tbody>
            </table>
        </div>
      </div>
@endsection

@section('js')
		<script type="text/javascript" src="{{ asset('js/movements/transfers/STransfersCore.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/transfers/tables.js')}}"></script>
@endsection
