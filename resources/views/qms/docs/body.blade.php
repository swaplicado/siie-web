<div>
    <div class="panel panel-default" v-for="vSection in vlSections">
        <div class="panel-heading" style="background-color:cadetblue">@{{ vSection.title }}</div>
        <div class="panel-body">
            <div class="row" v-for="vConfig in vlConfigurations" v-if="vConfig.section_id == vSection.id_section">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <b>@{{ vConfig.element }}</b>
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div :class="getDivClass(vConfig.n_values)" v-for="field in vConfig.lFields">
                                    @{{ field.field_name }}
                                </div>
                            </div>
                            <div class="row">
                                <div :class="getDivClass(vConfig.n_values)" v-for="field in vConfig.lFields">
                                    <input type="text" v-model="lResults[vConfig.id_configuration+'_'+field.id_field]"
                                            class="form-control input-sm" 
                                            v-if="vConfig.element_type_id == vScqms.ELEM_TYPE.TEXT">
        
                                    <div v-else-if="vConfig.element_type_id == vScqms.ELEM_TYPE.USER">
                                        <select class="form-control input-sm" v-model="lResults[vConfig.id_configuration+'_'+field.id_field]">
                                            <option v-for="usr in lUsers"
                                                :selected="lResults[vConfig.id_configuration+'_'+field.id_field] == usr.id"
                                                :value="usr.id">
                                                @{{ usr.username }}
                                            </option>
                                        </select>
                                    </div>
        
                                    <input type="number" v-model="lResults[vConfig.id_configuration+'_'+field.id_field]"
                                            class="form-control input-sm" 
                                            step="0.1"
                                            v-else-if="vConfig.element_type_id == vScqms.ELEM_TYPE.DECIMAL">
        
                                    <input type="number" v-model="lResults[vConfig.id_configuration+'_'+field.id_field]"
                                            class="form-control input-sm" 
                                            step="1"
                                            v-else-if="vConfig.element_type_id == vScqms.ELEM_TYPE.INT">
        
                                    <input type="date" v-model="lResults[vConfig.id_configuration+'_'+field.id_field]"
                                            class="form-control input-sm" 
                                            v-else-if="vConfig.element_type_id == vScqms.ELEM_TYPE.DATE">
        
                                    <input type="checkbox" v-model="lResults[vConfig.id_configuration+'_'+field.id_field]"
                                            class="form-control input-sm" v-else-if="vConfig.element_type_id == vScqms.ELEM_TYPE.BOOL">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>