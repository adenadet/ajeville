<template>
<section class="container-fluid">
    <div class="modal fade" id="planModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title" v-html="editMode ? 'Edit Plan' : 'Create Plan'"></h4>
                    <button type="button" class="close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body bg-white">
                    <InsuranceFormPlan :provider="provider" :plan.sync="plan" :editMode="editMode"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-dark">
            <h4 class="card-title">Plans</h4>
            <div class="card-tools"><button @click="newPlan()" class="btn btn-primary btn-xs"><i class="fa fa-plus mr-1"></i> Create New Plan</button></div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive border rounded p-0">
                <table class="table">
                    <thead>
                    <tr>
                        <th class="font-weight-bold">Name</th>
                        <th class="font-weight-bold">Patients</th>
                        <th class="font-weight-bold">Status</th>
                        <th class="font-weight-bold">Created at</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="plan in plans.data" :key="plan.id">
                            <td>{{ plan.name }}</td>
                            <td>{{ plan.patients != null ? plan.patients.length : 0 }}</td>
                            <td>{{ plan.status == 1 ? 'Active' : 'Inactive' }}</td>
                            <td>{{ ExcelDate(plan.created_at) }}</td>
                            <td>
                                <span class="nav-link" data-toggle="dropdown" href="#">
                                    <i class="fa fa-ellipsis-v"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <router-link :to="'/insurance/plans/'+plan.id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-2"></i> View Plan</router-link>
                                    <button class="btn btn-block dropdown-item" @click="updatePlan(plan)"><i class="fas fa-edit text-warning mr-2"></i> Update Plan</button>
                                    <button class="btn btn-block dropdown-item" @click="suspendPlan(plan.id)"><i class="fas fa-times text-danger mr-2"></i> Suspend Plan</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            form: new Form({}),
            plans: {},
            provider: {},
            providers: {},
            provider_types: [],
        }
    },
    mounted() {
        this.getAllInitials();
        /*this.$on('refreshPage', response => {
            this.refresh(response);    
            this.closeModal();
        });*/
    },
    methods: {
        closeModal(){
            $('#planModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            var route_id = this.provider_id != null ? this.provider_id :this.$route.params.id
            axios.get('/api/emr/insurance/plans/provider/'+route_id).then(response =>{
                this.refresh(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({
                    icon: 'error',
                    title: 'Visits were not loaded successfully',
                })
            });
        },
        newPlan(){
            this.editMode = false;
            this.plan = {};
            //this.$emit('planDataFill', {});
            $('#planModal').modal('show');
        },
        refresh(response){
            this.plans = response.data.plans;
            this.provider = response.data.provider;
        },
        suspendPlan(id){
            this.$swal.fire({
                title: 'Are you sure, you want to suspend this plan?',
                text: "You will be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, confirm it!'
                })
            .then((result) => {
                //Send Confirm request
                if(result.value){
                    this.form.delete('/api/emr/insurance/plans/'+id)
                    .then(response=>{
                        this.$swal.fire('Confirmed!', 'The Provider Plan has been suspended.', 'success');
                        this.refresh(response);   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        updatePlan(plan){
            this.loading = true;
            this.editMode = true;
            this.plan = plan;
            $('#planModal').modal('show');
            this.loading = false;
        },  
    },
}
</script>