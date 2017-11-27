@extends('userpermissions.create')
@section('menu')
	@include('templates.menu.menumodules')
@endsection
@section('title', trans('userinterface.titles.CREATE_USER_PERMISSION') . " " . \Auth::user()->username)
@section('content')
	{!! Form::open() !!}
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
      	<div>
					<!-- Nuevo form start -->
					<table class="table table-striped table-bordered table-hover table-condensed">
						<tr>
							<th>{!! trans('userinterface.SYS_PERMISSIONS') !!}</ht>
							<th>{!! trans('userinterface.MODULES') !!}</ht>
							<th>{!! trans('userinterface.PERMISSIONS') !!}</ht>
							<th>{!! trans('userinterface.labels.COMPANY') !!}</ht>
							<th>{!! trans('userinterface.PRIVILEGES') !!}</ht>
						</tr>

						<?php
							for ($j=0; $j < 10; $j++) {
								echo "<tr>";
								for ($i=0; $i < 5 ; $i++) {
									echo "<td>".$i."</td>";
								}
									echo "</tr>";
							}
						?>

					</table>
				</div>
			</div>
			<div class="text-right">
				<input type="button" class="btn btn-primary" value="MODIFICAR">
		</div>
		</div>
	</div>
{!! Form::close() !!}
<!-- Nuevo form end   -->
@endsection
