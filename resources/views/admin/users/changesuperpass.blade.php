@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menu')
@endsection

<?php
	$aux = $user;
	$sRoute = 'admin.users.updatesuperpass';
  $sRoute2 = 'admin.users.index';
?>

@section('title', trans('userinterface.titles.CHANGE_PASS'))

@section('content')

  <div class="form-group">
    {!! Form::label('username', trans('userinterface.labels.NAME').'*') !!}
    {!! Form::text('username',
      isset($user) ? $user->username : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.NAME'), 'required', 'readonly']) !!}
  </div>

  <div class="form-group">
    {!! Form::label('password', trans('userinterface.labels.PASS_NEW').'*') !!}
    {!! Form::password('password', ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.PASSWORD'), 'required']) !!}
  </div>

@endsection
