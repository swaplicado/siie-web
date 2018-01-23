@extends('templates.start.mainStart')
<br />
<br />
@section('title', 'Selecciona Almacen')

@section('content')
  <?php
    $sRoute = "start.whs";
    $i = 0;
  ?>
  <div class="form-group">
      <div class="list-group">
        @foreach($whs as $userCompanyRow)
            <a href="" id="{{ $userCompanyRow->id_whs }}" class="list-group-item {{ $i == 0 ? 'active' : '' }}">
              <?php if($flag=0){ echo $userCompanyRow->name;}
                else{ echo $userCompanyRow->warehouses->name;} ?>
            </a>
          <?php $i++; ?>
        @endforeach
      </div>
  </div>
@endsection
<div class="form-group">
  <div class="list-group"
</div>

<script>


/*

    var iAccessId = 0;

    function cli(accessId) {
      iAccessId = accessId;
      document.cookie = "iAccessId=" + iAccessId + "";
    }
*/
    // Obtains the value of the selected item and saves it in a cookie
    function getValue() {
      var items = document.getElementsByClassName("list-group-item active");
      document.cookie = "WarehouseId=" + items[0].id;
    }

</script>
