var app = new Vue({
    el: '#the_index',
    data: {
      vlPapeletas: oData.lPapeletas,
      idCurrentDoc: 0,
      signatureArgox: '',
      signatureCoding: '',
      signatureMb: '',
      iconArgox: 'glyphicon glyphicon-remove',
      iconCoding: 'glyphicon glyphicon-remove',
      iconMb: 'glyphicon glyphicon-remove',
      oCurDocument: new SQDocument(),
      sPassClose: '',
      isClosed: false
    },
    methods: {
      getIcon(isClosed) {
        if (isClosed) {
          return 'glyphicon glyphicon-folder-open';
        }

        return 'glyphicon glyphicon-folder-close';
      },
      getLabel(isClosed) {
        if (isClosed) {
          return 'Abrir';
        }

        return 'Cerrar'
      },
      showSignatures(oQDocument) {
        this.oCurDocument = oQDocument;
        this.idCurrentDoc = oQDocument.id_document;
        this.iconArgox = 'glyphicon glyphicon-remove';
        this.iconCoding = 'glyphicon glyphicon-remove';
        this.iconMb = 'glyphicon glyphicon-remove';

        if (oQDocument.b_argox) {
          this.iconArgox = 'glyphicon glyphicon-ok';
        }
        if (oQDocument.b_coding) {
          this.iconCoding = 'glyphicon glyphicon-ok';
        }
        if (oQDocument.b_mb) {
          this.iconMb = 'glyphicon glyphicon-ok';
        }
        
        $("#sigModal").modal();
      },
      signDocument(signatureType, signaturee) {
        oGui.showLoading(5000);
        
        axios.post('../../siie/signatures', {
          signature: signaturee,
          id: this.idCurrentDoc,
          signature_type_id: signatureType
        })
        .then(res => {
          let idSignature = res.data;

          if (idSignature == 1) {
            oGui.showError('La contraseña no coincide.');
          }
          else if (idSignature == -1) {
            oGui.showError('No tienes autorización para firmar este documento.');
          }
          else if (idSignature > 1) {
            oGui.showOk();
            location.reload();
          }
        })
        .catch(function (error) {
          console.log(error);
        });

      },
      showModalClose(idDoc, isClosed) {
        this.sPassClose = '';
        this.idCurrentDoc = idDoc;
        this.isClosed = isClosed;

        $("#openCloseModal").modal();
      },
      openCloseDocument() {
        if (this.sPassClose == '') {
          oGui.showError('Debe introducir la contraseña');
          return;
        }

        oGui.showLoading(5000);
        
        axios.post('../qdocs/openclose', {
          signature: this.sPassClose,
          id: this.idCurrentDoc
        })
        .then(res => {
          let idSignature = res.data;

          if (idSignature == 1) {
            oGui.showError('La contraseña no coincide.');
          }
          else if (idSignature == -1) {
            oGui.showError('No tienes autorización para firmar este documento.');
          }
          else if (idSignature > 1) {
            oGui.showOk();
            location.reload();
          }
        })
        .catch(function (error) {
          console.log(error);
        });
      }
    },
  });