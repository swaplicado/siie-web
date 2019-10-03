var appStart = new Vue({
    el: '#id_start',
    data: {
      message: 'Hello Vue!',
      vlUserCompanies: globalData.lUserCompanies,
      vlBranches: globalData.lBranches,
      vlWhs: globalData.lWhs,
      iCompany: globalData.iCompany,
      iBranch: globalData.iBranch,
      iWarehouse: globalData.iWarehouse,
      bWhs: globalData.bWhs
    },
    methods: {
        companyChanged() {
            console.log('here');
            if (this.iCompany == 0) {
                return;
            }

            this.vlBranches = [];

            axios.get('./start/changecompany/' + this.iCompany)
            .then(res => {
                console.log(res);
                if (res.data.length > 0) {
                    this.iBranch = res.data[0].id_branch;
                    this.vlBranches = res.data;
                }
            })
            .catch(err => {
                console.log(err);
            })
        },
        branchChanged() {
            this.vlWhs = [];
            
            axios.get('./start/changebranch/' + this.iCompany + '/' + this.iBranch)
            .then(res => {
                console.log(res);
                if (res.data.length > 0) {
                    this.iWarehouse = res.data[0].id_whs;
                    this.vlWhs = res.data;
                }
            })
            .catch(err => {
                console.log(err);
            })
        }
    },
    mounted() {
        if (this.bWhs) {
            this.branchChanged();
        }
    },
  })