var app = new Vue({
  el: "#primary_app",
  data: {
    message: "Hello Vue!",
    iMode: 1,
    aModes: [1, 2, 3],
    iCurDoc: 0,
    aCurConfig: [0, 0],
    lDocumentTypes: [
      {
        id_link_type: 5,
        id_link: 2,
        name: "Aderezos",
        b_class: "btn btn-primary btn-lg btn-block"
      },
      {
        id_link_type: 5,
        id_link: 4,
        name: "Mayonesa",
        b_class: "btn btn-danger btn-lg btn-block"
      },
      {
        id_link_type: 4,
        id_link: 4,
        name: "Aerosoles",
        b_class: "btn btn-success btn-lg btn-block"
      },
      {
        id_link_type: 6,
        id_link: 14,
        name: "Aceites Saborizados",
        b_class: "btn btn-info btn-lg btn-block"
      },
      {
        id_link_type: 4,
        id_link: 10,
        name: "Vinagre",
        b_class: "btn btn-default btn-lg btn-block"
      },
      {
        id_link_type: 5,
        id_link: 12,
        name: "Polvos",
        b_class: "btn btn-warning btn-lg btn-block"
      },
      {
        id_link_type: 4,
        id_link: 5,
        name: "Jarabes",
        b_class: "btn btn-primary btn-lg btn-block"
      },
      {
        id_link_type: 4,
        id_link: 3,
        name: "Salsas",
        b_class: "btn btn-default btn-lg btn-block"
      },
      {
        id_link_type: 1,
        id_link: 1,
        name: "TODOS",
        b_class: "btn btn-success btn-lg btn-block"
      }
    ],
    lSections: [],
    lElementTypes: [],
    lAllSections: [],
    lAllElements: [],
    oSelectedSection: 0,
    lConfigurations: [],
    iCurSection: 0,
    oSection: new SSection(),
    oElement: new SElement(),
    lAllAnalysis: oGD.lAllAnalysis,
    isAnalysis: false,
    oConfiguration: null,
    lFields: []
  },
  methods: {
    /**
     * get the configurations and set values to Vue
     *
     * @param {int} iLinkType
     * @param {int} iLink
     */
    toConfiguration(iLinkType, iLink) {
      this.iMode = 2;
      this.aCurConfig = [iLinkType, iLink];
      this.lSections = [];

      oGui.showLoading(5000);
      axios
        .get("../configdocs/getsectionsdata", {
          params: {
            linkType: iLinkType,
            link: iLink,
            zone: oGD.cfgZone
          }
        })
        .then(res => {
          this.lElementTypes = res.data.lElementTypes;
          this.lAllSections = res.data.lAllSections;
          this.lAllElements = res.data.lAllElements;
          this.lConfigurations = res.data.lConfigurations;
          this.lSections = res.data.lSections;
          this.iCurSection = 0;
        })
        .catch(err => {
          console.log(err);
        });
    },
    /**
     * return a SAnalysis object based on id received
     *
     * @param {int} iAnalysis
     */
    getAnalysisById(iAnalysis) {
      if (iAnalysis <= 0) {
        return "NA";
      }

      let result = this.lAllAnalysis.find(obj => {
        return obj.id_analysis === iAnalysis;
      });

      return result;
    },
    /**
     * get the object SElementType based on id_element_type received
     *
     * @param {int} iType
     */
    getElementType(iType) {
      let result = this.lElementTypes.find(obj => {
        return obj.id_element_type === iType;
      });

      return result;
    },
    /**
     * get the fields of element and show the fields modal
     *
     * @param {SConfiguration} oCfg
     */
    setConfiguration(oCfg) {
      this.oConfiguration = oCfg;
      this.lFields = [];

      oGui.showLoading(5000);

      axios
        .get("../configdocs/getfields", {
          params: {
            ielement: this.oConfiguration.id_element
          }
        })
        .then(res => {
          let oElement = res.data.oElement;
          let fields = res.data.lFields;

          if (fields.length === 0) {
            this.lFields = [];
            for (let index = 0; index < oElement.n_values; index++) {
              const field = new SField();
              field.element_id = oElement.id_element;

              this.lFields.push(field);
            }
          } else {
            this.lFields = fields;
          }

          $("#fieldsModal").modal();
        })
        .catch(err => {
          console.log(err);
        });
    },
    /**
     * Create a new section and save it in the database
     */
    newSection() {
      oGui.showLoading(5000);

      axios
        .post("../sections", {
          title: this.oSection.title,
          dt_section: this.oSection.dt_section,
          comments: this.oSection.comments
        })
        .then(res => {
          let obj = res.data;
          this.lAllSections.push(obj);
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    /**
     * Add the selected section to the sections array
     */
    addSection() {
      if (
        this.aCurConfig.length == 0 ||
        this.aCurConfig[0] == 0 ||
        this.aCurConfig[1] == 0
      ) {
        alert("Error!");
        return;
      }

      console.log(this.oSelectedSection);
      this.lSections.push(this.oSelectedSection);
    },
    /**
     * set the current section and initialize the element obj
     *
     * @param {int} idSection
     */
    addElement(idSection) {
      this.iCurSection = idSection;

      this.oElement = new SElement();
    },
    /**
     * determine if the configuration will create a new Element or just a configuration
     */
    processElement() {
      if (this.oElement.id_element > 0) {
        this.newConfiguration();
      } else {
        this.newElement();
      }
    },
    /**
     * Create a new element and save it in the database
     */
    newElement() {
      oGui.showLoading(5000);

      axios
        .post("../elements", {
          element: this.oElement.element,
          n_values: this.oElement.n_values,
          element_type_id: this.oElement.element_type_id,
          analysis_id: this.oElement.analysis_id
        })
        .then(res => {
          let obj = res.data;
          this.oElement = obj;
          this.newConfiguration();
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    changeElement() {
      this.isAnalysis = this.oElement.analysis_id > 0;
    },
    /**
     * create a new configuration and save it in the database
     */
    newConfiguration() {
      oGui.showLoading(5000);

      axios
        .post("../configdocs", {
          item_link_type_id: this.aCurConfig[0],
          item_link_id: this.aCurConfig[1],
          section_id: this.iCurSection,
          element_id: this.oElement.id_element,
          config_zone_id: oGD.cfgZone
        })
        .then(res => {
          let obj = res.data;

          oGui.showOk();
        })
        .catch(function(error) {
          console.log(error);
        });

      location.reload();
    },
    /**
     * remove the configuration, set is_deleted = true
     *
     * @param {int} idConfiguration
     */
    removeConfiguration(idConfiguration) {
      oGui.showLoading(5000);

      axios
        .delete("../configdocs/" + idConfiguration)
        .then(res => {
          let obj = res.data;

          // let pos = -1;
          // for (let index = 0; index < this.lConfigurations.length; index++) {
          //     const conf = this.lConfiguration[index];
          //     if (obj.id_configuration == conf.id_configuration) {
          //         pos = index;
          //     }
          // }

          // if (pos > -1) {
          //     this.lConfigurations.splice(pos, 1);
          // }

          oGui.showOk();
        })
        .catch(function(error) {
          console.log(error);
        });

      location.reload();
    },
    /**
     * Add a new SField object to Array
     */
    addField() {
      this.lFields.push(new SField());
    },
    /**
     * Update the fields of current element
     */
    updateFields() {
      oGui.showLoading(5000);

      let jfields = JSON.stringify(this.lFields);
      axios
        .post("../elements", {
          fields: jfields,
          element: this.oConfiguration.id_element
        })
        .then(res => {
          let obj = res.data;
          this.oElement = obj;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    /**
     * Set the flag is_deleted to true and remove the field of fields array
     *
     * @param {int} idField
     * @param {String} sText
     */
    removeField(idField, sText) {
      var txt;
      var r = confirm(
        "Esta acción removerá el campo en todas las configuraciones. \n ¿Deseas continuar?"
      );

      if (r == true) {
        oGui.showLoading(5000);

        if (idField == 0) {
          let pos = -1;
          for (let index = 0; index < this.lFields.length; index++) {
            const f = this.lFields[index];

            if (f.id_field == idField && f.field_name == sText) {
              pos = index;
            }
          }

          if (pos > -1) {
            this.lFields.splice(pos, 1);
          }

          oGui.showOk();
          return;
        }

        axios
          .delete("../elementfield/" + idField)
          .then(res => {
            let obj = res.data;

            oGui.showOk();

            location.reload();
          })
          .catch(function(error) {
            console.log(error);
          });
      } else {
        console.log("You pressed Cancel!");
      }
    }
  }
});
