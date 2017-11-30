<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', 'Default')</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('chosen/chosen.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/general.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/navbars.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/checkboxes.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('trumbowyg/dist/ui/trumbowyg.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
	{{-- <link rel="stylesheet" type="text/css" href="{{ asset('bootstrap-toggle/css/bootstrap-toggle.min.css') }}"> --}}
	<link rel="stylesheet" type="text/css" href="{{ asset('font-awesome/css/font-awesome.min.css') }}">
	<link rel="stylesheet" href="{{asset('datePicker/css/bootstrap-datepicker3.css')}}">
  <link rel="stylesheet" href="{{asset('datePicker/css/bootstrap-datepicker.standalone.css')}}">
	<link rel="stylesheet" type="text/css" href="{{ asset('sweet-alert/sweetalert2.min.css') }}">

<!--
	<link rel="stylesheet" type="text/css" href="{{ asset('bootstrap-table/bootstrap-table.css') }}">
-->

</head>
