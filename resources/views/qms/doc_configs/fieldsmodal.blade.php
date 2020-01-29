<!-- Modal -->
<div id="fieldsModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Parámetros</h4>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="col-md-6 col-md-offset-1">
              <label>Nombre del campo</label>
            </div>
            <div class="col-md-2">
              <label>Valor por defecto</label>
            </div>
            <div class="col-md-1">
              <label>En reporte</label>
            </div>
          </div>
          <div class="row" v-for="oField in lFields">
            <div class="col-md-6 col-md-offset-1">
              <input class="form-control input-sm" type="text" v-model="oField.field_name">
            </div>
            <div class="col-md-2">
              <input class="form-control input-sm" type="text" v-model="oField.field_default_value">
            </div>
            <div class="col-md-1">
              <input type="checkbox" class="form-control input-sm" v-model="oField.is_reported">
            </div>
            <div class="col-md-1">
              <button type="button" class="btn btn-danger btn-sm" aria-label="Borrar" v-on:click="removeField(oField.id_field, oField.field_name)">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
              </button>
            </div>
            <br/>
            <br/>
          </div>
          <div class="row">
            <div class="col-md-1 col-md-offset-10">
              <button type="button" class="btn btn-success btn-sm" aria-label="Agregar" v-on:click="addField()">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
              </button>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" v-on:click="updateFields()" data-dismiss="modal">Guardar</button>
      </div>
    </div>

  </div>
</div>