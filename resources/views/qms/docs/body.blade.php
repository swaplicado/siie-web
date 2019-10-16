<div>
    <div class="panel panel-default" v-for="vSection in vlSections">
        <div class="panel-heading" style="background-color:cadetblue">@{{ vSection.title }}</div>
        <div class="panel-body">
            <div class="row" v-for="vConfig in vlConfigurations" v-if="vConfig.section_id == vSection.id_section">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">

                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div :class="getDivClass(vConfig.n_values, vConfig.element_type_id)" v-for="field in vConfig.lFields">
                                    @{{ field.field_name }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <b>@{{ vConfig.element }}</b>
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div :class="getDivClass(vConfig.n_values, vConfig.element_type_id)" v-for="field in vConfig.lFields">
                                    <input type="text" v-model="lResults[vConfig.id_configuration+'_'+field.id_field].result"
                                            class="form-control input-sm" 
                                            v-if="vConfig.element_type_id == vScqms.ELEM_TYPE.TEXT">
        
                                    <div v-else-if="vConfig.element_type_id == vScqms.ELEM_TYPE.USER">
                                        <select class="form-control input-sm" v-model="lResults[vConfig.id_configuration+'_'+field.id_field].result">
                                            <option v-for="usr in lUsers"
                                                :selected="lResults[vConfig.id_configuration+'_'+field.id_field].result == usr.id"
                                                :value="usr.id">
                                                @{{ usr.username }}
                                            </option>
                                        </select>
                                    </div>
        
                                    <input type="number" v-model="lResults[vConfig.id_configuration+'_'+field.id_field].result"
                                            class="form-control input-sm" 
                                            style="text-align: right;"
                                            step="0.1"
                                            v-else-if="vConfig.element_type_id == vScqms.ELEM_TYPE.DECIMAL">
        
                                    <input type="number" v-model="lResults[vConfig.id_configuration+'_'+field.id_field].result"
                                            class="form-control input-sm" 
                                            style="text-align: right;"
                                            step="1"
                                            v-else-if="vConfig.element_type_id == vScqms.ELEM_TYPE.INT">
        
                                    <input type="date" v-model="lResults[vConfig.id_configuration+'_'+field.id_field].result"
                                            class="form-control input-sm" 
                                            v-else-if="vConfig.element_type_id == vScqms.ELEM_TYPE.DATE">
        
                                    <input type="checkbox" v-model="lResults[vConfig.id_configuration+'_'+field.id_field].result"
                                            class="form-control input-sm" v-else-if="vConfig.element_type_id == vScqms.ELEM_TYPE.BOOL">
                                    <div v-else-if="vConfig.element_type_id == vScqms.ELEM_TYPE.FILE">
                                        {{-- <form action="../../../image" method="POST" enctype="multipart/form-data"> --}}
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <input type="file" 
                                                        :id="lResults[vConfig.id_configuration+'_'+field.id_field].id_tag + '1'"
                                                        class="form-control input-sm"
                                                        :name="lResults[vConfig.id_configuration+'_'+field.id_field].id_tag + '1'"
                                                        v-on:change="readFile(event, vConfig.id_configuration, field.id_field)" 
                                                        {{-- v-model="lResults[vConfig.id_configuration+'_'+field.id_field].result" --}}
                                                        class="form-control input-sm">
                                                    <input type="hidden" id="theid" name="theid" :value="lResults[vConfig.id_configuration+'_'+field.id_field].id_tag + '1'">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>@{{ lResults[vConfig.id_configuration+'_'+field.id_field].result }}</label>
                                                </div>
                                                <div class="col-md-4">
                                                    <img height="150" width="150" :id="lResults[vConfig.id_configuration+'_'+field.id_field].id_tag">
                                                </div>
                                                <div class="col-md-4">
                                                    <button class="btn btn-info" v-on:click="viewFile(vConfig.id_configuration, field.id_field)">Ver</button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    {{-- <input type="submit" value="Upload Image" name="submit"> --}}
                                                    <button class="btn btn-primary" v-on:click="guardar(vConfig.id_configuration, field.id_field)">Guardar</button>
                                                </div>
                                            </div>
                                        {{-- </form> --}}
                                        <br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>