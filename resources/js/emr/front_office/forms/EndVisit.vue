<template>
<div class="overlay-wrapper">
    <div class="card p-0">
        <div class="card-body">
            <div v-if="unpaidTransactions.length">
                <div class="alert alert-danger"><strong>Visit cannot be ended.</strong><br>The following services require payment.</div>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="trx in unpaidTransactions" :key="trx.id">
                            <td>{{ trx.item_name }}</td>
                            <td><span class="badge badge-warning">{{ trx.status_label }}</span></td>
                            <td>{{ trx.outstanding }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="pendingTransactions.length" class="mt-4">
                <div class="alert alert-warning">
                    Some services are still pending.  
                    Select services to defer or cancel before ending the visit.
                </div>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th width="40"></th>
                            <th>Service</th>
                            <th>Service Status</th>
                            <th>Transaction Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="trx in pendingTransactions" :key="trx.id">
                            <td>
                                <input type="checkbox"
                                    v-model="form.defer_items"
                                    :value="trx.id">
                            </td>

                            <td>{{ trx.item_name }}</td>

                            <td>
                                <span class="badge badge-info">
                                    {{ trx.service_status_label }}
                                </span>
                            </td>

                            <td><span class="badge badge-secondary">{{ trx.status_label }}</span></td>
                        </tr>
                        <tr>
                            <td colspan="4">No Pending Transaction</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- SECTION 3 : Confirmation -->
            <div v-if="canEndVisit" class="mt-4">
                <div class="alert alert-success">
                    All required conditions satisfied.  
                    You can now end this visit.
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="form.confirm_end">
                    <label class="form-check-label"> Confirm that this visit should be closed.</label>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <button class="btn btn-secondary"@click="$emit('close')">Cancel</button>

            <button class="btn btn-danger" :disabled="!form.confirm_end || !canEndVisit" @click="endVisit"> End Visit</button>
        </div>
    </div>
</div>
</template>
<script>
export default {
    computed:{
        canEndVisit(){
            return this.unpaidTransactions.length === 0
        }
    },    
    data(){
        return{
            form:{
                defer_items:[],
                confirm_end:false
            },
            loading:false,
            pendingTransactions:[],
            unpaidTransactions:[],
        }
    },
    emits:['refreshEndVisitForm'],
    methods:{
        endVisit(){
            axios.put(`/api/emr/hims/visits/${this.visit_id}/end`,this.form)
            .then(()=>{
                this.$emit('refreshEndVisitForm')
            })
        },
        loadData(){
            if (this.visit_id){
                axios.get(`/api/emr/hims/visits/${this.visit_id}/end-check`)
                .then(res=>{
                    this.unpaidTransactions = res.data.unpaid
                    this.pendingTransactions = res.data.pending
                })
            }
            else{
                return;
            }
        },
    },
    mounted(){
        this.loadData()
    },
    props:{
        visit_id: Number
    },
    watch:{
        visit_id(){
            this.loadData();
        }
    }


}
</script>