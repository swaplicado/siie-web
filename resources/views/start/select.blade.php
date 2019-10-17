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
                style="background-color: lightsteelblue">
          <option v-for="company in vlCompanies" 
                  :value="company.id_company"
                  >@{{ company.name }}</option>
        </select>
    </div>
    <div class="form-group">
          {!! Form::label('branch', trans('userinterface.titles.SELECT_BRANCH').'*') !!}
          <select class="form-control input-lg" id="branch" name="branch" 
                  v-on:change="branchChanged()"
                  v-model="iBranch"
                  style="background-color: aliceblue">
            <option v-for="branch in vlBranches" :value="branch.id_branch">@{{ branch.name }}</option>
          </select>
    </div>
    <div class="form-group">
        {!! Form::label('whs', trans('userinterface.titles.SELECT_WHS').'*') !!}
        <select class="form-control input-lg" id="whs" name="whs" 
                v-model="iWarehouse"
                style="background-color: azure">
          <option v-for="whs in vlWarehouses" :value="whs.id_whs">@{{ whs.whs_code + '-' + whs.whs_name }}</option>
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
      this.lCompanies = <?php echo json_encode($lCompanies); ?>;
      this.iCompany = <?php echo json_encode($iCompany); ?>;
      this.iBranch = <?php echo json_encode($iBranch); ?>;
      this.iWarehouse = <?php echo json_encode($iWarehouse); ?>;
      this.bWhs = <?php echo json_encode(session()->has('whs')); ?>;
    }

    var globalData = new GlobalData();
    
  </script>
  <script src="{{ asset('js/siie/SStart.js') }}"></script>
@endsection
