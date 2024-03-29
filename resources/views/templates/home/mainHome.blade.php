<!DOCTYPE html>
<html lang="es">
	@include('templates.head')
	<body>
		<div class="container">
			<div class="row">
				<div class="panel panel-default">
				<div class="panel-heading col-md-12">
					<div class="col-md-8">
						<h2 class="panel-title">@yield('title')</h2>
					</div>
					<div style="text-align: right;" class="dropdown col-md-4">
						<button class="btn btn-default dropdown-toggle"
												type="button" id="dropdownMenu1"
												data-toggle="dropdown"
												aria-haspopup="true" aria-expanded="true">
							{{ session('company') == NULL ? 'Opciones' : session('company')->name }}
						  <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
							@if (\Auth::user()->user_type_id == \Config::get('scsys.TP_USER.ADMIN'))
								<li>
				            <a href="{{ route('plantilla.admin') }}">{{ trans('userinterface.ADMINISTRATOR') }}</a>
				        </li>
							@endif
							@if (App\SUtils\SValidation::hasPermission(\Config::get('scperm.PERMISSION.ERP')))
								<li>
										<a href="{{ route('siie.home') }}">{{ trans('siie.MODULE') }}</a>
								</li>
							@endif
				        <li>
				            <a href="{{ route('auth.logout') }}">{{ trans('userinterface.EXIT') }}</a>
				        </li>
			      </ul>
					</div>
				</div>
				<div class="panel-body">
					<section>
						@include('flash::message')
						@include('templates.error')
						<div class="col-md-12">
							@yield('content')
						</div>
					</section>
				</div>
			</div>
		</div>

		@include('templates.scripts')
		@yield('js')
		@yield('js_sync')

		</div>

		<footer>
			@include('templates.footer')
		</footer>

	</body>
</html>
