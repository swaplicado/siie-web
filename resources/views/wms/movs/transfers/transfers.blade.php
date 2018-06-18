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
                        <td style="text-align: center;">{{ $mov->mov_code.'-'.$mov->folio }}</td>
                        <td style="text-align: center;">{{ $mov->dt_date }}</td>
												<td>{{ $mov->src_branch_name }}</td>
												<td>{{ $mov->des_branch_name }}</td>
												<td align="right">{{ session('utils')->formatNumber($mov->total, \Config::get('scsiie.FRMT.QTY')) }}</td>
												<td align="right">{{ session('utils')->formatNumber($mov->indicted, \Config::get('scsiie.FRMT.QTY')) }}</td>
												<td style="text-align: center;">
				      						<?php
				      								$oRegistry = $mov;
				      								$iRegistryId = $mov->id_mvt;
				      								$loptions = [
				      									\Config::get('scsys.OPTIONS.EDIT'),
				      									\Config::get('scsys.OPTIONS.DESTROY'),
				      									\Config::get('scsys.OPTIONS.ACTIVATE')
				      								];
				      						?>
													<div>
															@include('templates.list.options')
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
