<!-- Modal -->
<div id="location_search_des" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 id="modal_title_loc" class="modal-title">{{ trans('wms.labels.LOCATION') }}</h4>
      </div>
      <div class="modal-body">
        <table id="locations_des_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
          <tbody>

          </tbody>
           {{-- <tfoot>
               <tr>
                 <th>ItemId</th>
                 <th>UnitId</th>
                 <th data-priority="1">Código</th>
                 <th data-priority="1">Nombre</th>
                 <th data-priority="1">Unidad</th>
               </tr>
           </tfoot> --}}
        </table>
      </div>
      <div class="modal-footer">
        <button id="select_button_loc_des" type="button" class="btn btn-default" data-dismiss="modal">{{ trans('actions.SSELECT') }}</button>
      </div>
    </div>
  </div>
</div>
