<!-- Modal -->
<div id="mat_prod_search" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('siie.labels.ITEM_SEARCH') }}</h4>
      </div>
      <div class="modal-body">
        <table id="search_items_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
          <tbody>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button id="select_item_button" type="button" class="btn btn-default" data-dismiss="modal">{{ trans('actions.SSELECT') }}</button>
      </div>
    </div>
  </div>
</div>
