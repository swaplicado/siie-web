@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $title)

<?php $sRoute='wms.docs'?>

@section('content')
  @section('thefilters')
		{!! Form::open(['route' => [ $sRoute.'.index', $iDocCategory, $iDocClass, $iDocType, $iViewType, $title],
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
	<div class="row">
		<table id="docTable" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
								<th data-priority="1">Folio</th>
		            <th data-priority="2">Fecha</th>
		            <th data-priority="2">Ref</th>
		            <th data-priority="1">Asociado de negocios</th>
		            <th>RFC</th>
								@if ($iViewType == Config::get('scwms.DOC_VIEW.NORMAL'))
									<th>Cantidad</th>
			            <th>Cant. procesada</th>
			            <th>Avance %</th>
			            <th>Cant. pendiente</th>
									<th>Ver</th>
									<th>Cerrar</th>
								@else
									<th>Código</th>
									<th>Material/Producto</th>
									<th>Cantidad</th>
									<th>Surtida</th>
									<th>Avance %</th>
									<th>Pendiente</th>
									<th>Un.</th>
								@endif
								@if ($iDocClass == \Config::get('scsiie.DOC_CLS.ADJUST') && $iDocType == \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE'))
									<th data-priority="1">Devolver</th>
								@else
									<th data-priority="1">Surtir</th>
								@endif
								<th>Ligar</th>
								<th>ID ERP</th>
								<th>Status</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($documents as $doc)
						<tr>
                {{-- <td class="small">{{ \Carbon\Carbon::parse($doc->dt_date)->format('d-m-Y') }}</td>
                <td class="small">{{ \Carbon\Carbon::parse($doc->dt_doc)->format('d-m-Y') }}</td> --}}
                <td class="small">{{ $doc->folio }}</td>
                <td class="small">{{ $doc->dt_doc }}</td>
                <td class="small">{{ $doc->num_src }}</td>
		            <td class="small">{{ $doc->name }}</td>
		            <td class="small">{{ $doc->cve_an }}</td>
								@if ($iViewType == Config::get('scwms.DOC_VIEW.NORMAL'))
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->qty_doc, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->qty_sur + $doc->qty_sur_ind, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber((($doc->qty_sur + $doc->qty_sur_ind) * 100)/$doc->qty_doc, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->qty_doc - ($doc->qty_sur + $doc->qty_sur_ind), \Config::get('scsiie.FRMT.QTY')) }}</td>
									<td>
										<a href="{{ route('siie.docs.view', $doc->id_document) }}" title="Ver documento"
																																class="btn btn-info btn-sm">
											<span class=" glyphicon glyphicon-eye-open" aria-hidden = "true"/>
										</a>
									</td>
									<td>
										<a href="{{ route('wms.docs.openclose', [\Config::get('scsiie.DOC_OPER.CLOSE'), $doc->id_document]) }}" title="Cerrar para surtido"
																																class="btn btn-default btn-sm">
											<span class="glyphicon glyphicon-ban-circle" aria-hidden = "true"/>
										</a>
									</td>
								@else
									<td class="small">{{ $doc->cve_item }}</td>
									<td class="small">{{ $doc->item }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->qty_row, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->qty_sur + $doc->qty_sur_ind, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">
										{{ session('utils')->formatNumber((($doc->qty_sur + $doc->qty_sur_ind) * 100) / $doc->qty_row, \Config::get('scsiie.FRMT.QTY')) }}
									</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->pending, \Config::get('scsiie.FRMT.QTY')) }}</td>
									<td class="small">{{ $doc->unit }}</td>
								@endif
								<td style="text-align: center;">
									@if ($doc->doc_sys_status_id != \Config::get('scsiie.DOC_SYS_STATUS.ANNULLED'))
										<?php
										if ($iDocCategory == \Config::get('scsiie.DOC_CAT.PURCHASES')) {
											if ($iDocClass == \Config::get('scsiie.DOC_CLS.ADJUST') &&
											$iDocType == \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE')) {
												$iMvtInvType = \Config::get('scwms.MVT_TP_OUT_PUR');
											}
											else {
												$iMvtInvType = \Config::get('scwms.MVT_TP_IN_PUR');
											}


										}
										else {
											if ($iDocClass == \Config::get('scsiie.DOC_CLS.ADJUST') &&
											$iDocType == \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE')) {
												$iMvtInvType = \Config::get('scwms.MVT_TP_IN_SAL');
											}
											else {
												$iMvtInvType = \Config::get('scwms.MVT_TP_OUT_SAL');
											}
										}

										switch ($iDocClass) {
											case \Config::get('scsiie.DOC_CLS.DOCUMENT'):
											$iDocSource = $doc->doc_src_id;
											$iDocDestiny = $doc->id_document;
											break;
											case \Config::get('scsiie.DOC_CLS.ORDER'):
											$iDocSource = 0;
											$iDocDestiny = $doc->id_document;
											break;
											case \Config::get('scsiie.DOC_CLS.ADJUST'):
											$iDocSource = $doc->doc_src_id;
											$iDocDestiny = $doc->id_document;
											break;

											default:
											$iDocSource = 1;
											$iDocDestiny = 1;
											break;
										}
										?>
										<a href="{{ route('wms.movs.supply', [$iMvtInvType, $title, $doc->id_document, 0]) }}" title="Surtir documento"
											class="btn btn-default btn-sm">
											<span class="glyphicon glyphicon-import" aria-hidden = "true"/>
										</a>
									@else
										<span style="color: red;" title="Anulado" class="glyphicon glyphicon-ban-circle" aria-hidden = "true"/>
									@endif
								</td>
								<td>
									@if (($doc->doc_src_id != 1 && ($doc->supp_ord > 0 || $doc->supp_cn > 0)) || ($doc->supp_inv > 0 && $doc->doc_src_id == 1))
										<a href="{{ route('wms.docs.link', [$iDocSource, $iDocDestiny]) }}" title="Enlazar surtido"
																																class="btn btn-default btn-sm">
											<span class="glyphicon glyphicon-link" aria-hidden = "true"/>
										</a>
									@else
										--
									@endif
								</td>
								<td class="small">{{ $doc->external_id }}</td>
								<td class="small">
									@if (! $doc->is_deleted)
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

@section('js')
	@include('templates.stock.scriptsstock')
	<script src="{{ asset('moment/moment.js') }}"></script>
	<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
	<script src="{{ asset('js/docs/docsbysupp.js') }}"></script>
@endsection
