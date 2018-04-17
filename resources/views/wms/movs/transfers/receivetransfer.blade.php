@extends('templates.basic_form')

@section('head')
	@include('templates.head')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $sTitle)
@section('titlepanel', $sTitle)

@section('content')
  @include('wms.movs.transfers.header')
  @include('wms.movs.transfers.searchpanel')
	@include('wms.movs.transfers.table')
@endsection


@section('js')
		<script type="text/javascript" src="{{ asset('js/movements/transfers/SExternalTransfersCore.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/transfers/SGuiTransfers.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/transfers/tables.js')}}"></script>
@endsection
