<template>
<section class="overlay-wrapper">
    <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateApprovalMatrix() : createApprovalMatrix()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input class="form-control" v-model="approvalMatrix.name" type="text" name="name" id="name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" v-model="approvalMatrix.status" name="status" id="status">
                        <option value=""> --Select Status--</option>
                        <option value=1>Active</option>
                        <option value=0>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-12 table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Role</th>
                                <th>Stage Name</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(stage, index) in approvalMatrix.stages" :key="index">
                                <td>{{ addOne(index) }}</td>
                                <td>
                                    <select class="form-control" v-model="approvalMatrix.stages[index].role_name">
                                        <option value="">--Select Role--</option>
                                        <option v-for="role in roles" :value="role.name">{{ role.name }}</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" v-model="approvalMatrix.stages[index].name" placeholder="Stage Name">
                                </td>
                                <td>
                                    <button class="btn btn-block dropdown-item" @click="deleteApprovalMatrixStage(index)"><i class="fa fa-trash mr-1 text-danger"></i> Delete </button> 
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>                                        
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            aapprovalMatrix: new Form({
                additional_cost: '',
                stages: [
                    {
                        name: 'Approve',
                        role_name: 'CEO',
                    },
                ],
            }),
            loading: false,
        }
    },
    emits:['refreshPage'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        updateAdditionalCost(){
            this.loading = true;
            this.approvalMatrix.put('/api/procurement/purchase_orders/update/'+this.purchase_order.id)
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Vendor has been assigned', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/procurement/vendors/initials')
            .then(response => {
                this.categories = response.data.categories;
                this.vendors = response.data.vendors;
                this.loading = false;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Vendor Assign Form did not loaded successfully',})
                this.loading = false;
            });
        },
        
    },
    props: {
        editMode: Boolean,
        item_type: String,
        item: Object,
        purchase_order: Object,
    },
    watch:{
        item(){
            if (this.item.vendor != null){
                this.aapprovalMatrix.vendor_id = this.item.vendor.id;
                this.aapprovalMatrix.category_id = this.item.vendor.category_id;
            }
            if (this.item_type == 'purchase_order'){this.aapprovalMatrix.po_id = this.item.id;}
            else if (this.item_type == 'work_order'){this.aapprovalMatrix.wo_id = this.item.id;}
        },
    }
}
</script>