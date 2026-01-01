<template>
    <section class="overlay-wrapper p-0">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <form id="register_form" >    
            <div class="row">
                <div class="col-sm-8">
                    <div class="form-group">
                        <label>Plan Name *</label>
                        <div class="form-control" id="nok_name" name="nok_name" placeholder="Full Name *" disabled>
                            <p v-if="plan != null && plan.id != null">{{ plan.name }}</p>
                            <p v-else>No Chosen Plan</p>
                        </div>
                        <input type="hidden" v-model="BranchAllocationData.plan_id" name="plan_id" id="plan_id" />
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Status *</label>
                        <select class="form-control" id="status" name="status" v-model="BranchAllocationData.status" required>
                            <option value="">--Select Status--</option>
                            <option value=1>Active</option>
                            <option value=0>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Branch</label>
                        <select class="form-control" name="branch_id" id="branch_id" v-model="BranchAllocationData.branch_id">
                            <option value="">--Select Branch</option>
                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Price List</label>
                        <select class="form-control" name="price_list_id" id="price_list_id" v-model="BranchAllocationData.price_list_id">
                            <option value="">--Select Price List--</option>
                            <option v-for="price_list in price_lists" :key="price_list.id" :value="price_list.id">{{ price_list.name }}</option>
                        </select>
                    </div>
                </div>
            </div>    
            <button @click.prevent="editMode ? updatePlanBranch() : createPlanBranch() " type="submit" name="submit" class="submit btn btn-success">Submit </button>
        </form>
    </section>
</template>
<script>
export default {
    data() {
        return {
            BranchAllocationData: new Form({
                branch_id: '',
                id: '',
                plan_id: '',
                price_list_id: '',
                status: '',
            }),
            branches: [],
            loading: false,
            price_lists: [],
            plan: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        createPlanBranch(){
            this.loading = true;
            this.BranchAllocationData.post('/api/emr/insurance/plan_branches')
            .then(response => {
                this.$Progress.finish();
                this.$emit('refreshPlanBranch', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'New Location has been added',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(() => {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
        },
        getInitials(){
            axios.get('/api/emr/insurance/plan_branches/initials').then(response =>{
                this.branches = response.data.branches;
                this.price_lists = response.data.price_lists;
                this.$Progress.finish();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Visits were not loaded successfully',
                })
            }); 
        },
        updatePlanBranch(){
            this.loading = true;
            this.BranchAllocationData.put('/api/emr/insurance/plan_branches/'+this.BranchAllocationData.id)
            .then(response => {
                this.$Progress.finish();
                this.$emit('refreshPlanBranch', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'Location has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(() => {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
        },
    },
    props: {
        editMode: Boolean,
        provider: Object,
        provider_types: Array,
        
    }
}
</script>