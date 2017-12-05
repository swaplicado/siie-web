@extends('templates.home.modules')

@section('title', trans('siie.MODULE'))

@section('content')

  <div class="row">
    @include('templates.home.rapidaccess')
    <?php echo createBlock(asset('images/wms/box.gif'), route('siie.importation'), trans('wms.QRY_INVENTORY'), "success3", trans('wms.QRY_INVENTORY_T'));?>
    <?php echo createBlock(asset('images/wms/movsan.gif'), "#", trans('wms.MOV_WAREHOUSES'), "success3", trans('wms.MOV_WAREHOUSES_T'));?>
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
            timer: 25000,
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
