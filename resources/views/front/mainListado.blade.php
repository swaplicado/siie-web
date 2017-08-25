<!DOCTYPE html>
<html lang="es">
	@include('front.head')
<body>
			@include('front.templates.menu')

	<div class="container">

	<div class="row">
		<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title">@yield('title')</h2>
		</div>
		<div class="panel-body"></div>
		<div class="col-md-12">
		@include('flash::message')
		@include('front.templates.error')
		@include('front.templates.listado')
		</div>
		<div class="panel-body">
			<section>
				@yield('content')
			</section>

		</div>
		<div class="col-md-4">
		</div>
	</div>
	</div>
	</div>

@include('front.templates.scripts')

</body>
<footer>
	@include('front.templates.footer')
</footer>
</html>
