<!--A Design by W3layouts
Author: W3layout
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
@extends('templates.basic')

@section('head')
  <link rel="stylesheet" type="text/css" href="{{ asset('css/error.css') }}">
@endsection
<body>
  @section('body')
    <div class="wrap">
   	<div class="logo">
   			<p>@yield('text', 'lorem')</p>
         <h2>ERROR</h2>
         <div class="sub">
             @yield('back')
    			</div>
         <h1>@yield('code', '00')</h1>

   	</div>
    </div>
   	<div class="footer">
   	 Design by-<a href="http://w3layouts.com">W3Layouts</a>
   	</div>
  @endsection
</body>
