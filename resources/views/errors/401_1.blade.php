@extends('errors.templates.tmperror')

@section('title', '401')

@section('text', trans('messages.401_1'))
@section('code', '401')
@section('back')
    <a href="{{ route('auth.logout') }}">{{ trans('userinterface.EXIT') }}</a>
@endsection
