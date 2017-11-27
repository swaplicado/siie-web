@extends('templates.list.mainlist')
@section('menu')
@include('templates.menu.menumodules')
@endsection
@section('addfilters')
@include('templates.list.search')
@endsection
@section('title', trans('userinterface.titles.LIST_ASSIGNAMENTS'))
@section('content')
<?php $sRoute="admin.userpermissions"?>
@section('create')
@include('templates.form.create')
@endsection
<table data-toggle="table" class="table table-condensed">
   <thead>
      <th>{{ trans('userinterface.labels.NAME') }}</th>
      <th>{{ trans('userinterface.labels.STATUS') }}</th>
      <th>{{ trans('userinterface.labels.ACTION') }}</th>
   </thead>
   <tbody>
      @foreach($users as $user)
      <tr>
         <td>{{ $user->username }}</td>
         <td>
            @if (! $user->is_deleted)
            <span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
            @else
            <span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
            @endif
         </td>
         <td>
            <?php
               $oRegistry = $user;
               $iRegistryId = $user->id_user;
               $loptions = [
               	\Config::get('scsys.OPTIONS.EDIT'),
               	\Config::get('scsys.OPTIONS.DESTROY'),
               	\Config::get('scsys.OPTIONS.ACTIVATE'),
               ];
               ?>
            {{--  --}}
            <?php
               $v_id = $iRegistryId;
               $v_route_edit = $sRoute.'.edit';
               $v_route_destroy = $sRoute.'.destroy';
               $v_created_by = $oRegistry->created_by_id;
               ?>
            @include('templates.list.edit')
            @include('templates.list.destroy')
            <div class="btn-group">
               <button type="button" class="btn btn-secondary dropdown-toggle btn-sm" data-toggle="dropdown">
               <span  class="caret"></span>
               </button>
               <ul class="dropdown-menu" role="menu">
                  @include('templates.list.activate')
                  <li class="divider"></li>
                  @include('templates.list.usraccess')
                  @include('templates.list.branch')
                  @include('templates.list.address')
               </ul>
            </div>
            {{--  --}}
         </td>
      </tr>
      @endforeach
   </tbody>
</table>
{!! $users->render() !!}
@endsection
