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

        axios.post('../../../../qdocs', {
          vdoc: JSON.stringify(this.vDocument),
          configurations: JSON.stringify(this.vlConfigurations),
          results: JSON.stringify(aResults)
        })
        .then(res => {
            console.log(res);

            oGui.showOk();

            location.reload();
        })
        .catch(function (error) {
            console.log(error);
        });
      },
      readFile(file, idConf, idField) {
        this.lResults[idConf + "_" + idField].data = file.target.files[0];
        this.lResults[idConf + "_" + idField].result = file.target.files[0].name;
      }
    },
    mounted: function () {
      console.log(this.vDocument);
      console.log(this.vMongoDocument);

      let results = [];
      if (this.vMongoDocument == null) {
        for (const config of this.vlConfigurations) {
          for (const field of config.lFields) {
            let val = null;

            switch (config.element_type_id) {
              case this.vScqms.ELEM_TYPE.TEXT:
                val = "";
                break;

              case this.vScqms.ELEM_TYPE.DECIMAL:
                val = 0.0;
                break;

              case this.vScqms.ELEM_TYPE.INT:
                val = 0;
                break;

              case this.vScqms.ELEM_TYPE.DATE:
                val = '2019-01-01';
                break;

              case this.vScqms.ELEM_TYPE.ANALYSIS:
                val = 0;
                break;

              case this.vScqms.ELEM_TYPE.BOOL:
                val = false;
                break;

              case this.vScqms.ELEM_TYPE.USER:
                val = 1;
                break;

              case this.vScqms.ELEM_TYPE.FILE:
                val = null;
                break;
            
              default:
                val = 0;
                break;
            }

            let oResult = new SResult(config.id_configuration, field.id_field, val);

            oResult.field_name = field.field_name;
            oResult.element_id = config.element_id;
            oResult.element_type_id = config.element_type_id;
            oResult.item_link_type_id = config.item_link_type_id;
            oResult.item_link_id = config.item_link_id;
            oResult.analysis_id = config.analysis_id;
            oResult.is_table = config.is_table;
            oResult.table_name = config.table_name;
            oResult.dt_date = new Date();

            results['' + config.id_configuration + '_' + field.id_field] = oResult;
          }
        }

        // console.log(Object.keys(fruits));  // ['0', '1', '2', '5']
        this.lResults = results;
      }
      else {
        let results = this.vMongoDocument.results;
        let aResults = [];
        for (const res of results) {
          aResults['' + res.id_configuration + '_' + res.id_field] = res;
        }

        this.lResults = aResults;
      }
    }
  })