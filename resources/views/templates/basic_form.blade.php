<!DOCTYPE html>
<html>
  <head>
    <title>@yield('title', 'Default')</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    @yield('head')
  </head>
  <body>

    @yield('menu')

    <div class="container">
      <div class="row">
        <div class="panel panel-default">
          <div class="panel-heading">
      			<h2 class="panel-title">@yield('title')</h2>
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
      <br />
    </div>

    </div>
    @include('templates.scripts')

  	@yield('js')

  </body>
  <footer>
    @yield('footer')
  </footer>
</html>