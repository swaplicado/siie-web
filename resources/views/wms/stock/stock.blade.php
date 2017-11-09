@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', trans('userinterface.titles.WHS_STOCK'))

@section('addfilters')
	@include('templates.stock.filterstk')
@endsection

@section('content')
	<br />
	<table id="table_id" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
	    <thead>
	        <tr class="titlerow">
	            <th>Item</th>
	            <th>Unidad</th>
	            <th>Tarima</th>
	            <th>Lote</th>
	            <th>Ubicación</th>
	            <th>Almacén</th>
	            <th>Entradas</th>
	            <th>Salidas</th>
	            <th>Existencia</th>
	        </tr>
	    </thead>
	    <tbody>
				@foreach ($data as $row)
					<tr>
	            <td>{{ $row->item }}</td>
	            <td>{{ $row->unit }}</td>
	            <td>{{ $row->pallet }}</td>
	            <td>{{ $row->lot_ }}</td>
							<td>{{ $row->location }}</td>
	            <td>{{ $row->warehouse }}</td>
	            <td>{{ $row->inputs }}</td>
	            <td>{{ $row->outputs }}</td>
	            <td>{{ $row->stock }}</td>
	        </tr>
				@endforeach
	    </tbody>
	</table>
@endsection

@section('js')
	@include('templates.stock.scriptsstock')
	<script>

	</script>
@endsection
