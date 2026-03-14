<template>
<div class="card">
    <div class="card-header bg-dark">
      <h4>Insurance</h4>
    </div>

  <div class="card-body">

    <div class="row">
      <div class="col-md-4">
        <select class="form-control"
                v-model="insurance_type_id"
                @change="loadProviders">
          <option value="">Insurance Type</option>
          <option v-for="t in providerTypes"
                  :key="t.id"
                  :value="t.id">
            {{ t.name }}
          </option>
        </select>
      </div>

      <div class="col-md-4">
        <select class="form-control"
                v-model="provider_id"
                @change="loadPlans">
          <option value="">Provider</option>
          <option v-for="p in filteredProviders"
                  :key="p.id"
                  :value="p.id">
            {{ p.name }}
          </option>
        </select>
      </div>

      <div class="col-md-4">
        <select class="form-control"
                v-model="selectedPlan">
          <option v-for="plan in filteredPlans"
                  :key="plan.id"
                  :value="plan">
            {{ plan.name }}
          </option>
        </select>
      </div>
    </div>

    <button class="btn btn-dark mt-2"
            @click="addPlan">
      Add
    </button>

  </div>
</div>
</template>
<script>
export default {
    data(){
        return{
            insurance_type_id:'',
            provider_id:'',
            selectedPlan:null,
            filteredProviders:[],
            filteredPlans:[]
        }
    },
    methods:{
        loadProviders(){
        let type = this.providerTypes.find(t => t.id == this.insurance_type_id)
        this.filteredProviders = type?.providers || []
        },

        loadPlans(){
        let provider = this.providers.find(p => p.id == this.provider_id)
        this.filteredPlans = provider?.plans || []
        },

        addPlan(){
        if(!this.selectedPlan) return
        this.form.insurances.push({
            ...this.selectedPlan,
            enrollee_id:'',
            expiry_date:''
        })
        }
    },
    props:['form','providerTypes','providers','plans'],
}
</script>