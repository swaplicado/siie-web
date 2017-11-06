@if(isset($aux))
	@if (isset($bIsCopy))
		{!! Form::open(['route' => [$sRoute, $aux], 'method' => 'POST']) !!}
		@yield('content')
		<div class="form-group" align="right">
			{!! Form::submit(trans('actions.SAVE'), ['class' => 'btn btn-primary']) !!}
	@else
		{!! Form::open(['route' => [$sRoute, $aux], 'method' => 'PUT']) !!}
		@yield('content')
		<div class="form-group" align="right">
			{!! Form::submit(trans('actions.EDIT'), ['class' => 'btn btn-primary']) !!}
	@endif
@else
	{!! Form::open(['route' => $sRoute, 'method' => 'POST','onsubmit'=>'document.getElementById("submitBtn").disabled=true']) !!}
	@yield('content')
	<div class="form-group" align="right">
<<<<<<< HEAD
		{!! Form::submit(trans('actions.SAVE'), ['id' => 'submitBtn','class' => 'btn btn-primary']) !!}
=======
		{!! Form::submit(trans('actions.SAVE'), ['class' => 'btn btn-primary']) !!}
>>>>>>> 83698ab7f3d94b83ccc2b374df5f9a42c3ead95e
@endif
	<input type="button" name="{{ trans('actions.CANCEL') }}" value="{{ trans('actions.CANCEL') }}" class="btn btn-danger" onClick="location.href='{{ route($sRoute2) }}'">
	</div>
	{!! Form::close() !!}
