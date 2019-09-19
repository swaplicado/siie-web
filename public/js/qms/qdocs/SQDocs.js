var docsApp = new Vue({
    el: '#docsApp',
    data: {
      message: 'Hello Vue!',
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
      getDivClass(nValues) {
        if (nValues >= 6) {
          return 'col-md-1';
        }
        else if (nValues == 1) {
          return 'col-md-4';
        }

        return 'col-md-2';
      },
      saveDocument() {
        oGui.showLoading(5000);

        let aResults = [];
        for (const key in this.lResults) {
          let k = key.split('_');
          let obj = new SResult(k[0], k[1], this.lResults[key]);
          aResults.push(obj);
        }

        axios.post('../../../../qdocs', {
          vdoc: JSON.stringify(this.vDocument),
          configurations: JSON.stringify(this.vlConfigurations),
          results: JSON.stringify(aResults)
        })
        .then(res => {
            console.log(res);
            this.vDocument = res.data;
        })
        .catch(function (error) {
            console.log(error);
        });
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
            
              default:
                val = 0;
                break;
            }

            results['' + config.id_configuration + '_' + field.id_field] = val;
          }
        }

        // console.log(Object.keys(fruits));  // ['0', '1', '2', '5']
        this.lResults = results;
      }
      else {
        let results = this.vMongoDocument.results;
        let aResults = [];
        for (const res of results) {
          aResults['' + res.id_configuration + '_' + res.id_field] = res.result;
        }

        this.lResults = aResults;
      }
    }
  })