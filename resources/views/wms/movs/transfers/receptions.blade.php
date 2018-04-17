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
      <div class="row">
        <div class="col-md-12">
          <table id="receptions_table" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="100%">
              <thead>
                  <tr class="titlerow">
                      <th>id_mov</th>
                      <th>Folio</th>
                      <th>Pendiente</th>
                      <th>Sucursal origen</th>
                      <th>Sucursal destino</th>
                      <th>Recibir</th>
                  </tr>
              </thead>
              <tbody>
                  @foreach ($lList as $mov)
                      <tr>
                        <td>{{ $mov->id_mvt }}</td>
                        <td>{{ $mov->folio }}</td>
                        <td>{{ $mov->increment - $mov->decrement }}</td>
                        <td>{{ $mov->src_branch_name }}</td>
                        <td>{{ $mov->des_branch_name }}</td>
												<td>
													<a href="{{ route('wms.movs.receivetransfer', [$mov->id_mvt]) }}" class="btn btn-default btn-md">
														<i class="glyphicon glyphicon-log-in"></i>
													</a>
												</td>
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
