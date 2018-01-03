@extends('templates.start.mainStart')
<br />
<br />
@section('title', 'Seleccione Sucursal')

@section('content')
  <?php
    $sRoute = "start.branch";
    $i = 0;
  ?>
  <div class="form-group">
      <div class="list-group">
        @foreach($branch as $userCompanyRow)
            <a href="" id="{{ $userCompanyRow->id_branch }}" class="list-group-item {{ $i == 0 ? 'active' : '' }}">
              {{ $userCompanyRow->name }}
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
      document.cookie = "BranchId=" + items[0].id;
    }

</script>
