<template>
<section class="container-fluid">
    <div class="modal fade" id="planModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-html="editMode ? 'Edit Plan' : 'Create Plan'"></h4>
                    <button type="button" class="close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body bg-white">
                    <InsuranceFormPlan :plan="plan" :editMode="editMode"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="planBranchModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Branch Allocation</h4>
                    <button type="button" class="close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body bg-white">
                    <InsuranceFormPlanBranch :editMode="editMode"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <InsuranceDetailSummaryPlan :plan="plan" />
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">List of Location</h3>
                    <div class="card-tools">
                        <button class="btn btn-xs btn-default" @click="addLocation"><i class="fa fa-plus mr-1"></i>Add New Location</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Branch</th>
                                <th>Price List</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(plan_branch, index) in plan_branches">
                                <td>{{ index | addOne }}</td>
                                <td>{{ plan_branch.branch != null ? plan_branch.branch.name : 'No Branch Assigned' }}</td>
                                <td>{{ plan_branch.price_list != null ? plan_branch.price_list.name : 'No Price List Assigned'}}</td>
                                <td>
                                <span class="nav-link" data-toggle="dropdown" href="#">
                                    <i class="fa fa-ellipsis-v"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <button class="btn btn-block dropdown-item" @click="updateLocation(plan_branch)"><i class="fas fa-edit mr-2 text-primary"></i> Update Plan Branch</button>
                                    <button class="btn btn-block dropdown-item" @click="deactivateLocation(plan_branch)"><i class="fas fa-power-off mr-2 text-danger"></i> Deactivate Plan in Branch</button>
                                </div>
                            </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            categories: [],
            contacts: [],
            editMode: false,
            provider: {},
            provider_types: [],
            plans: [], 
            plan: {},
            plan_branches: [],
        }
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('refreshPage', response => {
            this.refresh(response)
        });
        Fire.$on('updatedPlan', response => {
            this.refresh(response)
        });

    },
    methods: {
        addLocation(){
            this.editMode = false;
            Fire.$emit('planBranchUpdate', this.plan);
            $('#planBranchModal').modal('show');
        },
        closeModal(){
            $('#planBranchModal').modal('hide');
            $('#contactModal').modal('hide');
            $('#planModal').modal('hide');
            $('#planBranchModal').modal('hide');
            $('#providerModal').modal('hide');
        },
        deactivateLocation(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to deactivate this plan at this location!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/emr/insurance/plan_branches/'+id)
                    .then(response=>{
                        Swal.fire('Deactivated!', 'Plan has been deactivated.', 'success');
                        this.refresh(response);  
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/emr/insurance/plans/'+this.$route.params.id).then(response =>{
                this.refresh(response);
                this.$Progress.finish();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Plan was not loaded successfully',
                })
            });
        },
        refresh(response){
            this.plan = response.data.plan;
            this.plan_branches = response.data.plan_branches;
            this.closeModal();
        },
        updateLocation(plan_branch){
            this.editMode = true;
            Fire.$emit('planBranchUpdate', this.plan);
            Fire.$emit('planBranchDataFill', plan_branch);
            $('#planBranchModal').modal('show');
        },
        
    },
}
</script>