@extends('templates.start.mainStart')
<br />
<br />
@section('title', trans('userinterface.titles.SELECT_COMPANY'))

@section('content')
  <?php
    $sRoute = "start.getIn";
    $i = 0;
  ?>
  <div class="form-group">
      <div class="list-group">
        @foreach($lUserCompany as $userCompanyRow)
            <a href="" id="{{ $userCompanyRow->company_id }}" class="list-group-item {{ $i == 0 ? 'active' : '' }}">
              {{ $userCompanyRow->company->name }}
            </a>
          <?php $i++; ?>
        @endforeach
      </div>
  </div>
  <div class="form-group">
    {!! Form::label('work_date', trans('userinterface.labels.WORK_DATE').'*') !!}
    {!! Form::date('work_date', \Carbon\Carbon::now(), ['class' => 'form-control', 'id' => 'work_date']) !!}
  </div>
@endsection
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
      var date = document.getElementById("work_date");
      document.cookie = "iCompanyId=" + items[0].id;
      document.cookie = "tWorkDate=" + date.value;
    }

</script>
