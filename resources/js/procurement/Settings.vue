<template>
    <section class="card">
        <div class="card-header">
            <h3 class="card-title">General Settings</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" @click="addPurchaseOrderItem()"><i class="fas fa-plus"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-dark">
                            <h3 class="card-title">Requires Authorization</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="checkbox" v-model="settingData.work_order_requires_approval" class="custom-control-input" id="customSwitch3" @change="testProcess()">
                                    <label class="custom-control-label" for="customSwitch3">Work Order requires authorization</label>
                                    <select v-if="settingData.work_order_requires_approval == true" class="form-control" id="work_order_authorization" name="work_order_authorization">
                                        <option value="">--Select Approval Matrices--</option>
                                        <option v-for="matrix in approval_matrices" :value="matrix.id">{{matrix.name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="checkbox" class="custom-control-input" id="customSwitch3">
                                    <label class="custom-control-label" for="customSwitch3">Purchase Order requires authorization</label>
                                </div>
                            </div>
                        </div>        
                    </div>
                </div>
    
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"></h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group"></div>
                        </div>
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
            approval_matrices: [],
            editMode: false,
            form: new Form({}),
            loading: true,
            settingData: new Form({
                purchase_order_approval_matrix_id: '',
                purchase_order_requires_approval: false,
                work_order_approval_matrix_id: '',
                work_order_requires_approval: false,
            }),
        };
    },
    emits:['purchaseOrderReload'],
    methods: {
        testProcess(){
            alert("Working" + this.settingData.work_order_requires_approval);
        },
        addPurchaseOrderItem(){
            this.loading = true;
            this.purchase_order_item = {};
            this.editMode = false;
            $('#purchaseOrderItemFormModal').modal('show');  
            this.loading = false;
        },
        approvePurchaseOrder(){
            this.loading = true;
            $('#approvalFormModal').modal('show');  
            this.loading = false;
        },
        assignVendor(){
            this.loading = true;
            this.editMode = false;
            $('#assignVendorFormModal').modal('show');  
            this.loading = false;
        },
        cancelPurchaseOrder(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Cancel Purchase Order!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/procurement/purchase_orders/'+this.purchase_order.id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Purchase Order has been cancelled.', 'success');
                        this.getInitials();   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        closeModal(){
            $('#additionalCostFormModal').modal('hide');
            $('#approvalFormModal').modal('hide');  
            $('#assignVendorFormModal').modal('hide');  
            $('#confirmFulfillFormModal').modal('hide');  
            $('#orderItemFormModal').modal('hide');
            $('#purchaseOrderItemFormModal').modal('hide');
            $('#viewFulfillModal').modal('hide');
        },
        confirmFulfillment(){
            this.loading = true;
            $('#confirmFulfillFormModal').modal('show');  
            this.loading = false;
        },
        deletePurchaseOrder() {
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/procurement/purchase_orders/'+this.purchase_order.id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Purchase Order has been rejected.', 'success');
                        this.getInitials();  
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        deletePurchaseOrderItem(id) {
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/procurement/purchase_order_items/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Item has been deleted.', 'success');
                        this.getAllInitials();   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        fulfillPurchaseOrder(){
            this.loading = true;
            this.editMode = false;
            $('#fulfillFormModal').modal('show');  
            this.loading = false;
        },
        generateGRN(order_item){
            alert("Working")
        },
        getInitials(){
            this.loading = true;
            this.closeModal();
            this.$emit('purchaseOrderReload');
            this.loading = false; 
        },
        rejectPurchaseOrder(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "The purchase order will have to be restarted all over!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/procurement/purchase_orders/'+this.purchase_order.id)
                    .then(response=>{
                        this.$swal.fire('Rejected!', 'The Purchase Order has been rejected.', 'success');
                        this.getInitials();   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });

        },
        submitPurchaseOrder(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Submit Purchase Order!'
            })
            .then((result) => {
                if(result.value){
                    this.form.get('/api/procurement/purchase_orders/submit/'+this.purchase_order.id)
                    .then(response=>{
                        this.$swal.fire('Submitted!', 'Purchase Order has been submitted for approval.', 'success');
                        this.getInitials()
                    })
                    .catch(()=>{this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});});
                }
            });
        },
        updatePurchaseOrderItem(purchase_order_item){
            this.loading = true;
            this.purchase_order_item = purchase_order_item;
            this.editMode = true;
            $('#purchaseOrderItemFormModal').modal('show');  
            this.loading = false;
        },
        updateAdditionalCost(){
            this.loading = true;
            $('#additionalCostFormModal').modal('show');  
            this.loading = false;
        },
        viewFulfillment(){
            this.loading = true;
            $('#viewFulfillModal').modal('show');  
            this.loading = false;
        },
    },
    mounted(){
        this.getInitials();
    },
};
</script>