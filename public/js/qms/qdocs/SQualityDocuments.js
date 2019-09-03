var app = new Vue({
    el: '#primary_app',
    data: {
        message: 'Hello Vue!',
        iMode: 1,
        aModes: [1, 2, 3],
        iCurDoc: 0,
        aCurConfig: [0, 0],
        lDocumentTypes: [
                        {
                            'id_link_type': 5,
                            'id_link': 2,
                            'name': 'Aderezos',
                            'b_class': 'btn btn-primary btn-lg btn-block'
                        },
                        {
                            'id_link_type': 5,
                            'id_link': 4,
                            'name': 'Mayonesa',
                            'b_class': 'btn btn-danger btn-lg btn-block'
                        },
                        {
                            'id_link_type': 6,
                            'id_link': 64,
                            'name': 'Aerosol',
                            'b_class': 'btn btn-success btn-lg btn-block'
                        },
                        {
                            'id_link_type': 1,
                            'id_link': 1,
                            'name': 'Saborizados',
                            'b_class': 'btn btn-info btn-lg btn-block'
                        },
                        {
                            'id_link_type': 4,
                            'id_link': 10,
                            'name': 'Vinagre',
                            'b_class': 'btn btn-default btn-lg btn-block'
                        },
                        {
                            'id_link_type': 5,
                            'id_link': 12,
                            'name': 'Polvos',
                            'b_class': 'btn btn-warning btn-lg btn-block'
                        },
                        {
                            'id_link_type': 4,
                            'id_link': 5,
                            'name': 'Jarabes',
                            'b_class': 'btn btn-primary btn-lg btn-block'
                        },
                        {
                            'id_link_type': 4,
                            'id_link': 3,
                            'name': 'Salsas',
                            'b_class': 'btn btn-default btn-lg btn-block'
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
        
        toConfiguration(iLinkType, iLink) {
            this.iMode = 2;
            this.aCurConfig = [iLinkType, iLink];
            this.lSections = [];

            oGui.showLoading(5000);
            axios.get('../qms/configdocs/getsectionsdata', {
                    params: {
                        linkType: iLinkType,
                        link: iLink
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
            })
            
        },
        getAnalysisById(iAnalysis) {
            if (iAnalysis <= 0) {
                return 'NA';
            }

            let result = this.lAllAnalysis.find(obj => {
                            return obj.id_analysis === iAnalysis
                        });
                        
            return result;
        },
        getElementType(iType) {
            let result = this.lElementTypes.find(obj => {
                            return obj.id_element_type === iType
                        });
                        
            return result;
        },
        setConfiguration(oCfg) {
            this.oConfiguration = oCfg;
            this.lFields = [];

            oGui.showLoading(5000);
            axios.get('../qms/configdocs/getfields', {
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
                }
                else {
                    this.lFields = fields;
                }

                $("#fieldsModal").modal();
            })
            .catch(err => {
                console.log(err);
            })
        },
        newSection() {
            oGui.showLoading(5000);

            axios.post('../qms/sections', {
                title: this.oSection.title,
                dt_section: this.oSection.dt_section,
                comments: this.oSection.comments
            })
            .then(res => {
                let obj = res.data;
                this.lAllSections.push(obj);
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        addSection() {
            if (this.aCurConfig.length == 0 || this.aCurConfig[0] == 0 || this.aCurConfig[1] == 0) {
                alert('Error!');
                return;
            }

            console.log(this.oSelectedSection);
            this.lSections.push(this.oSelectedSection);
        },
        addElement(idSection) {
            this.iCurSection = idSection;

            this.oElement = new SElement();
        },
        processElement() {
            if (this.oElement.id_element > 0) {
                this.newConfiguration();
            }
            else {
                this.newElement();
            }
        },
        newElement() {
            oGui.showLoading(5000);

            axios.post('../qms/elements', {
                element: this.oElement.element,
                n_values: this.oElement.n_values,
                element_type_id: this.oElement.element_type_id
            })
            .then(res => {
                let obj = res.data;
                this.oElement = obj;
                this.newConfiguration();
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        newConfiguration() {
            oGui.showLoading(5000);

            axios.post('../qms/configdocs', {
                item_link_type_id: this.aCurConfig[0],
                item_link_id: this.aCurConfig[1],
                section_id: this.iCurSection,
                element_id: this.oElement.id_element
            })
            .then(res => {
                let obj = res.data;
                let sec = this.lSections.splice((this.lSections.length - 1), 1);

                let section = new SSection();
                section.id_section = sec[0].id_section;
                section.title = sec[0].title;
                section.dt_section = sec[0].dt_section;
                section.comments = sec[0].comments;
                section.is_deleted = sec[0].is_deleted;

                this.lConfigurations.push(obj);
                this.lSections.push(section);
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        updateFields() {
            oGui.showLoading(5000);

            let jfields = JSON.stringify(this.lFields);
            axios.post('../qms/elements', {
                fields: jfields,
                element: this.oConfiguration.id_element
            })
            .then(res => {
                let obj = res.data;
                this.oElement = obj;
            })
            .catch(function (error) {
                console.log(error);
            });
        }
    }
  })