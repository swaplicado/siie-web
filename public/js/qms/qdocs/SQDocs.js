var docsApp = new Vue({
    el: '#docsApp',
    data: {
      vScqms: oData.scqms,
      vData: oData.data,
      vDocument: oData.oQDocument,
      vMongoDocument: oData.oMongoDocument,
      lUsers: oData.lUsers,
      vlSections: oData.lSections,
      vlConfigurations: oData.lConfigurations,
      lResults: []
    },
    methods: {
      getDivClass(nValues, elemTypeId) {
        if (nValues > 6) {
          if (elemTypeId != this.vScqms.ELEM_TYPE.DECIMAL) {
            return 'col-md-1';
          }
        }
        else if (nValues == 1) {
          if (elemTypeId == this.vScqms.ELEM_TYPE.FILE) {
            return 'col-md-6';
          }

          return 'col-md-4';
        }

        return 'col-md-2';
      },
      saveDocument() {
        oGui.showLoading(5000);

        let aResults = [];
        for (const key in this.lResults) {
          let res = this.lResults[key];

          aResults.push(res);
        }

        let route = '';
        if (oData.source == 0) {
          route = '../../../../../qdocs';
        }
        else {
          route = '../../../qdocs';
        }

        axios.post(route, {
          vdoc: JSON.stringify(this.vDocument),
          configurations: JSON.stringify(this.vlConfigurations),
          zone: JSON.stringify(oData.cfgZone),
          results: JSON.stringify(aResults)
        })
        .then(res => {
            console.log(res);

            if (res.data == -1) {
              oGui.showError('La papeleta está cerrada');
              return;
            }

            oGui.showOk();

            location.reload();
        })
        .catch(function (error) {
            console.log(error);

            oGui.showError('Ocurrió un error al guardar la papeleta');
        });
      },
      /**
       * load the image to img tag when the file is selected
       * 
       * @param {Event} evt 
       * @param {int} idConf 
       * @param {int} idField 
       */
      readFile(evt, idConf, idField) {
        let tgt = evt.target;
        let files = tgt.files;
        let index = idConf + "_" + idField;
        let tag = this.lResults[index].id_tag;
        let fileImg = files[0];

        this.lResults[index].result = fileImg.name;
        this.lResults[index].updated_at = new Date();
        this.lResults[index].usr_upd = oData.usr;
        

        // FileReader support
        if (FileReader && files && files.length) {
          var fr = new FileReader();
          fr.onload = function () {
            document.getElementById(tag).src = fr.result;
          }
          fr.readAsDataURL(fileImg);
        }
      },
      /**
       * load the file with the name assigned in server
       * @param {*} idConf 
       * @param {*} idField 
       */
      viewFile(idConf, idField) {
        var _img = document.getElementById(this.lResults[idConf + "_" + idField].id_tag);
        if (this.lResults[idConf + "_" + idField].data == null || this.lResults[idConf + "_" + idField].data.length == 0) {
          oGui.showError('No se ha seleccionado ninguna imágen...');
          return;
        }
        var newImg = new Image;
        newImg.onload = function() {
            _img.src = this.src;
        }

        let route = '';
        if (oData.source == 0) {
          route = '../../../../../uploads/qms/';
        }
        else {
          route = '../../../../uploads/qms/';
        }

        newImg.src = route + this.lResults[idConf + "_" + idField].data;
      },
      /**
       * save the image to server and return the name assigned to image
       * 
       * @param {int} idConf 
       * @param {int} idField 
       */
      guardar(idConf, idField) {
        var formData = new FormData();
        let imagefile = document.getElementById(this.lResults[idConf + "_" + idField].id_tag + '1');
        if (imagefile.files.length == 0) {
          oGui.showError('No se ha seleccionado ninguna imágen...');
          return;
        }
        formData.append("image", imagefile.files[0]);
        
        let route = '';
        if (oData.source == 0) {
          route = '../../../../qdocs/image';
        }
        else {
          route = '../../../qdocs/image';
        }

        axios.post(route, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        .then(res => {
            if (res.data == 'Error') {
              oGui.showError('Verifique que sea un formato de imagen válido y que el tamaño del archivo no sea mayor a 2048 KB');
            }
            else {
              this.lResults[idConf + "_" + idField].data = res.data;
              oGui.showOk();
            }
        })
        .catch(function (error) {
            console.log(error);
        });
      },
      setUpdate(idConf, idField) {
        this.lResults[idConf + "_" + idField].updated_at = new Date();
        this.lResults[idConf + "_" + idField].usr_upd = oData.usr;
      }
    },
    mounted: function () {
      let results = [];
      if (this.vMongoDocument == null || 
          (this.vMongoDocument != null && oData.scqms.CFG_ZONE.FQ == oData.cfgZone && this.vMongoDocument.results == undefined) ||
            (this.vMongoDocument != null && oData.scqms.CFG_ZONE.MB == oData.cfgZone && this.vMongoDocument.resultsMb == undefined)) {
        for (const config of this.vlConfigurations) {
          for (const field of config.lFields) {
            let oResult = new SResult(config.id_configuration, field.id_field, null);

            switch (config.element_type_id) {
              case this.vScqms.ELEM_TYPE.TEXT:
                oResult.result = "";
                break;

              case this.vScqms.ELEM_TYPE.DECIMAL:
                oResult.result = 0.0;
                break;

              case this.vScqms.ELEM_TYPE.INT:
                oResult.result = 0;
                break;

              case this.vScqms.ELEM_TYPE.DATE:
                oResult.result = '2019-01-01';
                break;

              case this.vScqms.ELEM_TYPE.ANALYSIS:
                oResult.result = 0;
                break;

              case this.vScqms.ELEM_TYPE.BOOL:
                oResult.result = false;
                break;

              case this.vScqms.ELEM_TYPE.USER:
                oResult.result = 1;
                break;

              case this.vScqms.ELEM_TYPE.FILE:
                oResult.result = null;
                oResult.id_tag = config.element.trim().replace(" ", "");
                break;
            
              default:
                oResult.result = 0;
                break;
            }

            oResult.field_name = field.field_name;
            oResult.is_reported = field.is_reported;
            oResult.element_id = config.element_id;
            oResult.element_type_id = config.element_type_id;
            oResult.item_link_type_id = config.item_link_type_id;
            oResult.item_link_id = config.item_link_id;
            oResult.analysis_id = config.analysis_id;
            oResult.is_table = config.is_table;
            oResult.table_name = config.table_name;
            oResult.dt_date = new Date();
            oResult.config_zone_id = oData.zfgZone;

            results['' + config.id_configuration + '_' + field.id_field] = oResult;
          }
        }

        // console.log(Object.keys(fruits));  // ['0', '1', '2', '5']
        this.lResults = results;
      }
      else {
        let results = []
        switch (oData.cfgZone) {
          case oData.scqms.CFG_ZONE.FQ:
            results = this.vMongoDocument.results;
            break;

          case oData.scqms.CFG_ZONE.MB:
            results = this.vMongoDocument.resultsMb;
            break;
        
          default:
            break;
        }

        this.lResults = [];
        for (const res of results) {
          this.lResults['' + res.id_configuration + '_' + res.id_field] = res;
        }
      }
    }
  })