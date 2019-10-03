@extends('templates.start.mainStart')
<br />
<br />
@section('title', trans('userinterface.titles.SELECT_COMPANY'))

@section('content')
  <div id="id_start">
    <?php
      $sRoute = "start.getIn";
      $i = 0;
    ?>
    <div class="form-group">
        {!! Form::label('company', trans('userinterface.titles.SELECT_COMPANY').'*') !!}
        <select class="form-control input-lg" id="company" name="company" 
                v-on:change="companyChanged()"
                v-model="iCompany"
                style="background-color: aliceblue">
          <option v-for="company in vlUserCompanies" 
                  :value="company.company.id_company"
                  >@{{ company.company.name }}</option>
        </select>
    </div>
    <div class="form-group">
          {!! Form::label('branch', trans('userinterface.titles.SELECT_BRANCH').'*') !!}
          <select class="form-control input-lg" id="branch" name="branch" 
                  v-on:change="branchChanged()"
                  v-model="iBranch"
                  style="background-color: lightgoldenrodyellow">
            <option v-for="branch in vlBranches" :value="branch.id_branch">@{{ branch.name }}</option>
          </select>
    </div>
    <div class="form-group">
        {!! Form::label('whs', trans('userinterface.titles.SELECT_WHS').'*') !!}
        <select class="form-control input-lg" id="whs" name="whs" 
                v-model="iWarehouse"
                style="background-color: linen">
          <option v-for="whs in vlWhs" :value="whs.id_whs">@{{ whs.name }}</option>
        </select>
    </div>
    <div class="form-group">
      {!! Form::label('work_date', trans('userinterface.labels.WORK_DATE').'*') !!}
      {!! Form::date('work_date', \Carbon\Carbon::now(), ['class' => 'form-control input-lg', 'id' => 'work_date']) !!}
    </div>
  </div>
@endsection
@section('js')

  <script>
    function GlobalData () {
      this.lUserCompanies = <?php echo json_encode($lUserCompany); ?>;
      this.lBranches = <?php echo json_encode($lBranches); ?>;
      this.lWhs = <?php echo json_encode($lWhs); ?>;
      this.iCompany = <?php echo json_encode($iCompany); ?>;
      this.iBranch = <?php echo json_encode($iBranch); ?>;
      this.iWarehouse = <?php echo json_encode($iWarehouse); ?>;
      this.bWhs = <?php echo json_encode(session()->has('whs')); ?>;
    }

    var globalData = new GlobalData();
    
  </script>
  <script src="{{ asset('js/siie/SStart.js') }}"></script>
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
