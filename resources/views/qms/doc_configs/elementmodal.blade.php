<!-- Modal -->
<div id="elementModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Nuevo Elemento</h4>
      </div>
      <div class="modal-body">
        <label for="title">Seleccione elemento</label>
        <select v-model="oElement" class="form-control" id="id_section" :required="true">
            <option v-for="oElem in lAllElements"
                    v-bind:value="oElem"
            >
                    @{{ oElem.element }}
            </option>
        </select>
        <br/>
        <label for="element">Nombre de elemento</label>
        <input class="form-control input-sm" type="text" id="element" v-model="oElement.element">
        <br/>
        <label for="dt_section">Fecha</label>
        <select v-model="oElement.element_type_id" class="form-control" id="id_section" :required="true">
            <option v-for="oEType in lElementTypes"
                    v-bind:value="oEType.id_element_type"
                    :selected="oElement.element_type_id == oEType.id_element_type"
            >
                    @{{ oEType.element_type }}
            </option>
        </select>
        <br/>
        <input type="checkbox" v-model="isAnalysis">
        <label for="checkbox">Es análisis</label>
        <br/>
        <div v-if="isAnalysis">
            <select v-model="oElement.analysis_id" class="form-control">
                <option v-for="oAnalysis in lAllAnalysis"
                        v-bind:value="oAnalysis.id_analysis"
                        :selected="oElement.analysis_id == oAnalysis.id_analysis"
                >
                        @{{ oAnalysis.name }}
                </option>
            </select>
        </div>
        <label for="comments"># Valores</label>
        <input class="form-control input-sm" type="text" id="n_values" v-model="oElement.n_values">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" v-on:click="processElement()">Agregar</button>
      </div>
    </div>

  </div>
</div>