<template>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <h3 class="col-12 card-title">Procurements</h3>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-shopping-cart"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Purchase Orders</span>
                        <span class="info-box-number">{{ purchase_orders.total }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-concierge-bell"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Work Orders</span>
                        <span class="info-box-number">{{ work_orders.total }}</span>
                    </div>
                </div>
            </div>
            <div class="clearfix hidden-md-up"></div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-list-alt"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Purchase Request List</span>
                        <span class="info-box-number">{{ sales_orders.total }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-clipboard-check"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">GRN Confirmation</span>
                        <span class="info-box-number">{{ sales_orders.total }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-tasks"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Job Completion Confirmation</span>
                        <span class="info-box-number">{{ sales_orders.total }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <h3 class="col-12 card-title">Sales</h3>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-cash-register"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Sales Order</span>
                        <span class="info-box-number">{{ sales_orders.total }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-reply"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Returns Request</span>
                        <span class="info-box-number">{{ returns.total }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <h3 class="col-12 card-title">Inventory</h3>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-indent"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Inward Transfer Orders</span>
                        <span class="info-box-number">{{ transfer_orders.total }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-outdent"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Outward Transfer Orders</span>
                        <span class="info-box-number">{{ transfer_orders.total }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <h3 class="col-12 card-title">Finances</h3>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-money-bill-alt"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Expenses</span>
                        <span class="info-box-number">{{ expenses?.total ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            editMode: false,
            expenses: [],
            goods_received: {data: [], total:0},
            loading: false,
            purchase_orders: {data: [], total: 0},
            returns: {data:[], total:0},
            sales_orders: {data: [], total: 0},
            transfer_orders: {data: [], total: 0},
            work_orders: {data: [], total: 0},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        approveOrder(order){
            this.loading = true;
            this.editMode = true;
            this.form_type = "accept";
            $('#orderModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#orderModal').modal('hide');
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/approvals/dashboard')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Dashboard loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Dashboard not loaded successfully',
                })
            });
        },
        issueRequest(){
            this.loading = true;
            this.editMode = true;
            this.form_type = "issue";
            $('#transferOrderModal').modal('show');
            this.loading = false;
        },
        refreshPage(response){
            this.transfer_orders = response.data.transfer_orders;
            this.sales_orders = response.data.sales_orders;
        },
    },
}
</script>