@extends('templates.start.mainStart')
<br />
<br />
@section('title',trans('userinterface.SELECT_WHS'))

@section('content')
  <?php
    $sRoute = "start.whs";
    $i = 0;
  ?>
  <div class="form-group">
      <div class="list-group">
        @foreach($whs as $userCompanyRow)
            <a href="" id=<?php if($flag==1){
                                echo $userCompanyRow->whs_id;
                              }
                  else{
                        echo $userCompanyRow->warehouses->id_whs;
                      } ?> class="list-group-item {{ $i == 0 ? 'active' : '' }}">
              <?php if($flag==1){
                                  echo $userCompanyRow->name;
                                }
                    else{
                          echo $userCompanyRow->warehouses->name;
                        } ?>
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
