<template>
    <section class="content">
        <ProcurementDetailPurchaseOrder :purchase_order.sync="purchase_order" view="direct" />
    </section>
</template>
<script>
export default {
    computed:{
        sub_total(){
            if (this.purchase_order.items == null){
                return 0.00
            }
            else{
                if(this.purchase_order.items.length == 0){
                    return 0.00;
                }
                else{
                    console.log(this.purchase_order.items[i].unit_price * this.purchase_order.items[i].quantity)
                    let sum = 0.00;
                    let i = 0;
                    while (i < this.purchase_order.items.length) {
                        sum = sum + (this.purchase_order.items[i].unit_price * this.purchase_order.items[i].quantity);
                        i++;
                        console.log
                    }
                    return sum;
                }
            }
        },
        total(){
            let sum = 0.00;
            sum = (this.purchase_order.taxes != null ? this.purchase_order.taxes: 0.00) + (this.purchase_order.logistics != null ? this.purchase_order.logistics: 0.00) - (this.purchase_order.discount != null ? this.purchase_order.discount: 0.00) 
            return sum;
        }
    },
    data() {
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            purchase_order: {},
            purchase_order_item: {},
            total_sub_total: 0,
            total_price: 0,
        };
    },
    methods: {
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
                        this.getAllInitials();   
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
            $('#orderItemFormModal').modal('hide');
            $('#purchaseOrderItemFormModal').modal('hide');
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
                        this.getAllInitials();  
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
        getAllInitials() {
            this.loading = true;
            this.closeModal();
            axios.get('/api/procurement/purchase_orders/'+this.$route.params.id)
            .then(response =>{
                this.purchase_order = response.data.purchase_order;
                let order_items = response.data.purchase_order.order_items;
                let sum = 0;
                for (let i = 0; i < order_items.length; i++) {
                    if (order_items[i].total_price == null || order_items[i].total_price == 0){sum = order_items[i].unit_price * (order_items[i].approved_quantity != 0 ? order_items[i].approved_quantity : order_items[i].quantity)}
                    else{sum = order_items[i].total_price}
                    this.total_sub_total = this.total_sub_total + sum;
                }
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Tickets were not loaded successfully',
                })
            });
        },
        rejectPurchaseOrder(){},
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
                //Send Delete request
                if(result.value){
                    this.form.get('/api/procurement/purchase_orders/submit/'+this.purchase_order.id)
                    .then(response=>{
                        this.$swal.fire('Submitted!', 'Purchase Order has been submitted for approval.', 'success');
                        //this.$route.push('')  
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
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
    },
    mounted(){
        this.getAllInitials();
    }
};
</script>