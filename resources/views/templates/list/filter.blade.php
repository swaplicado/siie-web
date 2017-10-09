{!! Form::open(['route' => [ $sRoute.'.index'],'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
	<div class="form-group">
    <div class="input-group">
	    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => trans('userinterface.placeholders.SEARCH'), 'aria-describedby' => 'search']) !!}
			@yield('addfilters')
	    <span class="input-group-btn">
	        <button id="searchbtn" type="submit" class="form-control">
						<span class="glyphicon glyphicon-search"></span>
					</button>
			</span>
    </div>
	</div>
{!! Form::close() !!}
