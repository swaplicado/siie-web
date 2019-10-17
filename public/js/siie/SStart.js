var appStart = new Vue({
    el: '#id_start',
    data: {
      message: 'Hello Vue!',
      vlCompanies: globalData.lCompanies,
      vlBranches: [],
      vlWarehouses: [],
      iCompany: globalData.iCompany,
      iBranch: globalData.iBranch,
      iWarehouse: globalData.iWarehouse,
      bWhs: globalData.bWhs
    },
    methods: {
        companyChanged() {
            if (this.iCompany == 0) {
                return;
            }
            this.vlBranches = this.vlCompanies[this.iCompany].oPartner.lBranches;
            this.iBranch = this.getFirstKey(this.vlBranches);
            this.branchChanged();
        },
        branchChanged() {
            this.vlWarehouses = this.vlCompanies[this.iCompany].oPartner.lBranches[this.iBranch].lWhs;
            this.iWarehouse = this.getFirstKey(this.vlWarehouses);
        },
        getFirstKey(data) {
            for (const key in data) {
                return key;
            }
        }
    },
    mounted() {
        this.vlBranches = this.vlCompanies[this.iCompany].oPartner.lBranches;
        this.vlWarehouses = this.vlCompanies[this.iCompany].oPartner.lBranches[this.iBranch].lWhs;
    },
  })