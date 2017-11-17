<!-- Modal -->
<div class="modal fade" id="myStock" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('wms.labels.STOCKS') }}</h4>
      </div>
      <br />
      <div id="divTable" class="modal-body">
        <table id="tblstock" class="table table-bordered display responsive no-wrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th data-override="id_tr"></th>
                    <th data-override="code">{{ trans('wms.labels.QTY') }}</th>
                    <th data-override="code">{{ trans('wms.labels.UNIT') }}</th>
                </tr>
            </thead>
            <tbody id='app'>
              <tr>
                <td>Liberado</td>
                <td align="right">@{{ stock.released }}</td>
                <td align="right">@{{ stock.unit }}</td>
              </tr>
              <tr>
                <td>Segregado</td>
                <td align="right">@{{ stock.segregated }}</td>
                <td align="right">@{{ stock.unit }}</td>
              </tr>
              <tr>
                <td>Disponible</td>
                <td align="right">@{{ stock.available }}</td>
                <td align="right">@{{ stock.unit }}</td>
              </tr>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button id="closeModal" type="button" class="btn btn-default" data-dismiss="modal">{{ trans('actions.CLOSE') }}</button>
      </div>
    </div>

  </div>
</div>
