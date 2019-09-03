<!-- Modal -->
<div id="sectionModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Nueva Sección</h4>
      </div>
      <div class="modal-body">
        <label for="title">Nombre de sección</label>
        <input class="form-control input-sm" type="text" id="title" v-model="oSection.title">
        <br/>
        <label for="dt_section">Fecha</label>
        <input class="form-control input-sm" type="date" id="dt_section" v-model="oSection.dt_section">
        <br/>
        <label for="comments">Comentarios</label>
        <input class="form-control input-sm" type="text" id="comments" v-model="oSection.comments">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" v-on:click="newSection()">Agregar</button>
      </div>
    </div>

  </div>
</div>