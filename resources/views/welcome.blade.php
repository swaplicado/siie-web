<!DOCTYPE html>
<html lang="es">
	@include('templates.head')
<body>
	<br />
	<div class="container">
	<div class="row">
		<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title">
				<div class="row">
					<div class="col-md-4">
						<font size="2%">Ave. Norte-sur 451, Cd Industrial, 58200 Morelia, Mich.</font>
					</div>
					<div style="text-align: center;" class="col-md-4">
						<font size="2%">(443) 2-04-10-32.     soporte@swaplicado.com.mx</font>
					</div>
					<div style="text-align: right;" class="col-md-4">
						<font size="2%">Software Aplicado SA de CV.-
									<a href="http://www.swaplicado.com.mx/"  target="_blank">
										www.swaplicado.com.mx
									</a>
						</font>
					</div>
				</div>
			</h2>
		</div>

		<div class="panel-body">
			<section>
				<div class="row">
					<div style="text-align: center;" class="col-md-4">
						<img src="{{ asset('images/siie.png') }}" alt="siie web">
					</div>
					<div style="text-align: center;" class="col-md-4">
						<img src="{{ asset('images/web.png') }}" alt="siie web">
					</div>
					<div style="text-align: center;" class="col-md-4">
						<img src="{{ asset('images/imageh.png') }}" alt="siie web">
					</div>
				</div>
				<div class="row">
					<div style="text-align: center;" class="col-md-3 col-md-offset-2">
						<a class="btn btn-success btn-lg" target="_blank"
								href="http://saporis.mx/">Saporis Comercial</a>
					</div>
					<div style="text-align: center;" class="col-md-3">
						<a class="btn btn-primary btn-lg" href="{{ route('auth.login') }}">Entrar al sistema</a>
					</div>
					<div style="text-align: center;" class="col-md-3">
						<a class="btn btn-info btn-lg" target="_blank"
							href="https://docs.google.com/document/d/1tYdh6WbB724pQdGyTVSIK-j6LUMaLXKuQtvA_E_BuzU/edit?usp=sharing">
							Manual de usuario
						</a>
					</div>
				</div>
				<br>
				<br>
				<div class="row">
					<div class="col-md-8 col-md-offset-2">
						<p>
									El SIIE Web pretende solucionar la problemática
									sufrida por las PyME del sector industrial en relación a
									la gestión de sus operaciones centrales: producción, calidad,
									almacenes y embarques.
						</p>
						<p>
							Sus principales funcionalidades son:
							<ul style="list-style-type:circle">
								<li>Entradas y salidas de almacén.</li>
								<li>Surtidos y devoluciones de pedidos y facturas de ventas y compras.</li>
								<li>Traspasos de almacén.</li>
								<li>Ajustes de almacén.</li>
								<li>Segregación y liberación de existencias.</li>
								<li>Emisión de etiquetas con códigos de barras de materiales y productos, tarimas y ubicaciones.</li>
								<li>Consulta de existencias e información de materiales y productos, tarimas y ubicaciones mediante códigos de barras.</li>
							</ul>
						</p>
					</div>
				</div>
			</section>
		</div>
	</div>
	</div>
	<br>
	<br>

	<script src="{{ asset('/jquery/js/jquery-3.2.1.js')}}"></script>
	<script src="{{ asset('bootstrap/js/bootstrap.js')}}"></script>
	<script src="{{ asset('chosen/chosen.jquery.js') }}"></script>
	<script src="{{ asset('Trumbowyg/dist/trumbowyg.min.js') }}"></script>

</div>
<footer>
	@include('templates.footer')
</footer>

</body>
</html>
