@extends('templates.home.modules')

@section('title', $title)

@section('content')

  <div class="row">
      <div class="col-md-3">
        <div class="row">
          <div class="col-md-9 col-md-offset-3">
            <a class="btn btn-primary btn-lg" onclick="holaFun()" href="{{ route('siie.import.items') }}" style="display:block;">1. {{ trans('siie.SYNCR.ITEMS') }}</a>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="row">
          <div class="col-md-9 col-md-offset-3">
            <a class="btn btn-success btn-lg" onclick="holaFun()" href="{{ route('siie.import.partners') }}">2. {{ trans('siie.SYNCR.PARTNERS') }}</a>
            <br />
            <a class="btn btn-success btn-lg" onclick="holaFun()" href="{{ route('siie.import.branches') }}">3. {{ trans('siie.SYNCR.BRANCHES') }}</a>
            <br />
            <a class="btn btn-success btn-lg" onclick="holaFun()" href="{{ route('siie.import.addresses') }}">4. {{ trans('siie.SYNCR.ADDRESSES') }}</a>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="row">
          <div class="col-md-7 col-md-offset-5">
              <a class="btn btn-info btn-lg" onclick="holaFun()" href="{{ route('siie.import.documents') }}">5. {{ trans('siie.SYNCR.DOCUMENTS') }}</a>
              <br />
              <a class="btn btn-info btn-lg" onclick="holaFun()" href="{{ route('siie.import.rows') }}">6. {{ trans('siie.SYNCR.ROWS1') }}</a>
              <br />
              <a class="btn btn-info btn-lg" onclick="holaFun()" href="{{ route('siie.import.rows1') }}">7. {{ trans('siie.SYNCR.ROWS2') }}</a>
              <br />
              <a class="btn btn-info btn-lg" onclick="holaFun()" href="{{ route('siie.import.taxes') }}">8. {{ trans('siie.SYNCR.TAXES1') }}</a>
              <br />
              <a class="btn btn-info btn-lg" onclick="holaFun()" href="{{ route('siie.import.taxes1') }}">9. {{ trans('siie.SYNCR.TAXES2') }}</a>
          </div>
        </div>
      </div>
  </div>

@endsection

@section('js')
  <script>
      var isImported = <?php echo json_encode($isImported); ?>;



      if (isImported == 1) {
        swal({
          type: 'success',
          title: 'Proceso finalizado',
          showConfirmButton: false,
          timer: 1500
          })
      }

      function holaFun() {
        swal({
            title: 'Espere...',
            text: 'Se está realizando el proceso de importación.',
            timer: 300000,
            onOpen: () => {
              swal.showLoading()
            }
          }).then((result) => {
            if (result.dismiss === 'timer') {
              console.log('I was closed by the timer');
            }
          });
      }
  </script>
@endsection
