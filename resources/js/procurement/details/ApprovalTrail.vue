<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-md-12">
            <div class="timeline">
                <div v-for="approval in approvals" :key="approval.id">
                    <i class="fas fa-check-double bg-success"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{ ExcelDate(approval.created_at) }}</span>
                        <h3 class="timeline-header"><a href="#">{{ FullName(approval.approver) }}</a> {{ approval.decision == 'confirm' ? ' confirmed ' : ' rejected ' }} this purchase order</h3>
                        <div class="timeline-body" v-html="approval.remark"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>  
</template>
<script>
export default{
    data() {
        return {
            editMode: false,
            form: new Form({}),
            loading: true,
        };
    },
    emits:['purchaseOrderDetailReload'],
    methods: {
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
            $('#purchaseOrderItemFormModal').modal('hide');
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
        //this.getAllInitials();
    },
    props:{
        approvals: Array,
    },
    watch:{
    },
};
</script>