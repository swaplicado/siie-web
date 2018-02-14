@extends('templates.basic_form')

@section('head')
  @include('templates.head.shead')
@endsection

@section('title', trans('mms.FORMULAS'))

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('titlepanel', trans('mms.FORMULAS'))

@section('content')
  <?php $sRoute="mms.formulas"?>

  @section('create')
    @include('templates.form.create')
  @endsection

  <div class="row">

    <table id="formulas_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
        <thead>
            <tr class="titlerow">
                <th data-priority="1">Folio</th>
                <th data-priority="1">Inicio Vigencia</th>
                <th data-priority="1">Fin Vigencia</th>
                <th data-priority="1">Fórmula</th>
                <th data-priority="1">Cantidad</th>
                <th data-priority="1">Duración</th>
                <th>Costo</th>
                <th>{{ trans('mms.labels.EXP') }}</th>
                <th>edit/elim/activar</th>
            </tr>
        </thead>
        <tbody>
          @foreach ($formulas as $formula)
            <tr>
                <td>{{ $formula->id_formula }}</td>
                <td>{{ \Carbon\Carbon::parse($formula->dt_start)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($formula->dt_end)->format('d-m-Y') }}</td>
                <td>{{ $formula->name }}</td>
                <td align="right">{{ session('utils')->formatNumber($formula->quantity, \Config::get('scsiie.FRMT.QTY')) }}</td>
                <td align="right">{{ session('utils')->formatNumber($formula->duration, \Config::get('scsiie.FRMT.QTY')) }}</td>
                <td align="right">{{ session('utils')->formatNumber($formula->cost, \Config::get('scsiie.FRMT.AMT')) }}</td>
                <td>
      						@if ($formula->is_exploded)
      								<span class="label label-success">{{ trans('userinterface.YES') }}</span>
      						@else
      								<span class="label label-danger">{{ trans('userinterface.NO') }}</span>
      						@endif
      					</td>
                <td>
      						<?php
      								$oRegistry = $formula;
      								$iRegistryId = $formula->id_formula;
      								$loptions = [
      									\Config::get('scsys.OPTIONS.EDIT'),
      									\Config::get('scsys.OPTIONS.DESTROY'),
      									\Config::get('scsys.OPTIONS.ACTIVATE'),
      								];
      						?>
      						@include('templates.list.options')
      					</td>
            </tr>
          @endforeach
        </tbody>
    </table>
  </div>
@endsection

@section('js')
  <script src="{{ asset('js/formulas/table.js')}}"></script>
@endsection

@section('footer')
    @include('templates.footer')
@endsection
