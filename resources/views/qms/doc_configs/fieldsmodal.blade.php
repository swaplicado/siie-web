<!-- Modal -->
<div id="fieldsModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Parámetros</h4>
      </div>
      <div class="modal-body">
          <div class="row" v-for="oField in lFields">
            <div class="col-md-9 col-md-offset-1">
              <input class="form-control input-sm" type="text" v-model="oField.field_name">
              <br/>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" v-on:click="updateFields()" data-dismiss="modal">Guardar</button>
      </div>
    </div>

  </div>
</div>