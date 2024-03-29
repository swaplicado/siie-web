<!DOCTYPE html>
<html lang="es">
	@include('templates.head')
<body>
	@yield('menu')

	<div class="container">

	<div class="row">
		<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title">@yield('title')</h2>
		</div>
		<div class="panel-body"></div>
		<div class="col-md-12">
			<div class="row">

			</div>
			<div class="row">
				<div class="col-md-1">
					@yield('create')
				</div>
				<div class="col-md-11">
					@include('templates.list.filter')
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					@include('flash::message')
					@if(isset($iId))
							@if($iId>0)
								<label>Imprimir etiquetas de tarima <?php echo $iId ?> : </label>
								<a href="{{ route('wms.pallets.print', $iId) }}" 
										target="_blank" class="btn btn-success btn-xs">	
									<span class="glyphicon glyphicon-save" aria-hidden="true"></span>
								</a>
							@endif
					@endif
					@include('templates.error')
				</div>

			</div>
		</div>
		<div class="panel-body">
			<section>
				@yield('content')
			</section>

		</div>
	</div>
	</div>
	</div>
@include('templates.scripts')

@yield('js')
@yield('js_sync')
</body>
<br />
<br />
<footer>
	@include('templates.footer')
</footer>
</html>
