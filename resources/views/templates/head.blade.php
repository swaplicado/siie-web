<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', 'SIIE Web')</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('chosen/chosen.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/general.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/navbars.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/checkboxes.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('datatables/datatables.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('sweet-alert/sweetalert2.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('daterangepicker/daterangepicker.css') }}">

	@include('templates.menu.info')
	@include('templates.menu.syncmms')
</head>
