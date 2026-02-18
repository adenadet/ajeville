<template>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sales</span>
                        <span class="info-box-number">{{ orders.total }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-reply"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Returns</span>
                        <span class="info-box-number">{{ returns.total }}</span>
                    </div>
                </div>
            </div>
            <div class="clearfix hidden-md-up"></div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Deliveries</span>
                        <span class="info-box-number">{{ delivery_notes.total }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">New Customers</span>
                        <span class="info-box-number">{{ customers.total }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow rounded">
                    <div class="card-header"><h3 class="card-title">Sales Trend</h3></div>
                    <div class="card-body p-0">
                        <apexchart  height="500" type="bar" :options="salesTrendOptions" :series="salesTrendSeries"></apexchart>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow rounded">
                    <div class="card-header"><h3 class="card-title">Customer Acquisition</h3></div>
                    <div class="card-body p-0">
                        <apexchart type="donut" height="500" :options="customer_acquistion_options" :series="customer_acquistion_series" />
                    </div>
                </div>
            </div>
            <!--div class="col-md-6">
                <div class="card shadow rounded">
                    <div class="card-header"><h3 class="card-title">Top Products / Categories</h3></div>
                    <div class="card-body p-0">
                        Put a graph here
                        <!-apexchart type="bar" height="300" :options="topProductsOptions" :series="topProductsSeries"/>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow rounded">
                    <div class="card-header"><h3 class="card-title">Refunds / Returns</h3></div>
                    <div class="card-body p-0">
                        Put a graph here
                        <!-apexchart type="donut" height="300" :options="refundOptions" :series="refundSeries"/->
                    </div>
                </div>
            </div-->
        </div>
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header border-transparent bg-navy">
                        <h3 class="card-title">List of Pending Orders</h3>
                    </div>
                    <div class="card-body p-0 table-responsive" style="height:400px;">
                        <SalesDetailOrderList :orders.sync="orders.data"  view="sales" @salesOrderReload="getAllInitials"/>
                    </div>
                    <div class="card-footer clearfix">
                        <router-link to="/sales_orders/orders" class="btn btn-sm btn-secondary float-right">View All Orders</router-link>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header border-transparent bg-secondary">
                        <h3 class="card-title">Latest Deliverables</h3>
                    </div>
                    <div class="card-body p-0 table-responsive" style="height:350px;">
                        <SalesDetailDeliveryNoteList :delivery_notes.sync="delivery_notes.data" source="dashboard" />
                    </div>
                    <div class="card-footer clearfix">
                        <router-link to="/sales_orders/delivery_notes" class="btn btn-sm btn-secondary float-right">View All Orders</router-link>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="info-box mb-3 bg-warning">
                    <span class="info-box-icon"><i class="fas fa-tag"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Inventory Items</span>
                        <span class="info-box-number">{{ items.total }}</span>
                    </div>
                </div>
                <div class="info-box mb-3 bg-success">
                    <span class="info-box-icon"><i class="fa fa-reply"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Returns</span>
                        <span class="info-box-number">{{ returns.total }}</span>
                    </div>
                </div>
                <div class="info-box mb-3 bg-secondary">
                    <span class="info-box-icon"><i class="fas fa-truck-moving"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Delivery Notes</span>
                        <span class="info-box-number">{{ delivery_notes.total }}</span>
                    </div>
                </div>
                <!--div class="info-box mb-3 bg-info">
                    <span class="info-box-icon"><i class="far fa-comment"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Direct Messages</span>
                        <span class="info-box-number">163,921</span>
                    </div>
                </div-->
                <div class="card">
                    <div class="card-header bg-warning"><h3 class="card-title">Recently Added Products</h3></div>
                    <div class="card-body p-0 table-responsive" style="height: 600px;"><InventoryDetailItemList :items.sync="items.data" source="dashboard" /></div>
                    <div class="card-footer text-center"><router-link to="/sales_orders/items" class="uppercase">View All Products</router-link></div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import VueApexCharts from "vue3-apexcharts";
import SalesDetailDeliveryNoteList from '@/sales_orders/details/DeliveryNoteList.vue';
import SalesDetailOrderList from '@/sales_orders/details/OrderList.vue';
import SalesFormDeliveryNote from '@/sales_orders/forms/DeliveryNote.vue';
export default {
    components: {
        SalesDetailDeliveryNoteList, SalesDetailOrderList, SalesFormDeliveryNote, 
        apexchart: VueApexCharts,
    },
    data(){
        return {
            customers: {total: 0,},
            customerSeries: [],
            customerOptions: [],
            customer_acquistion_options: {
                labels: ['New Customers', 'Returning Customers'],
                chart: {id: "customer-acquistion-chart",},
                xaxis: {labels: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998],},
            },
            customer_acquistion_series: [],
            chartCAOptions: {
                chart: {id: "customer-acquistion-chart",},
                xaxis: {labels: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998],},
            },
            series: [
                {name: "series-1", data: [30, 40, 35, 50, 49, 60, 70, 91],},
            ],
            delivery_notes: {data: [], total: 0,},
            items: {data: [], total: 0},
            orders: {data: [], total: 0,},
            products: {data: [], total: 0,},
            refundSeries: [],
            refundOptions: [],
            returns: {data: [], total: 0,},
            salesTrendSeries: [{
                name: 'months',
                data: [30, 40, 45, 50, 49, 60, 70, 91]
            }],
            salesTrendOptions: {
                chart: {id: "sales-trend-chart",},
                xaxis: {categories: [],},
            },
            series: [
                {name: "series-1", data: [30, 40, 35, 50, 49, 60, 70, 91],},
            ],
            topProductsSeries: [],
            topProductsOptions: [],
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/sales/dashboard')
            .then(response =>{
                this.customers = response.data.customers;
                this.customer_acquistion_series = [ response.data.customer_new, response.data.customer_repeat,];
                this.customerSeries.value = [ response.data.customer_new, response.data.customer_repeat,]
                this.deliverables = response.data.deliverables;
                this.delivery_notes = response.data.delivery_notes;
                this.items = response.data.items;
                this.orders = response.data.orders;
                this.returns = response.data.returns;
                this.transformSalesTrend(response.data.monthly_sales)    
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Dashboard not loaded successfully',});
            });
        },
        transformSalesTrend(monthly_sales) {
            // Get the last 6 months (including current)
            const months = []
            const now = new Date()
            for (let i = 5; i >= 0; i--) {
                const d = new Date(now.getFullYear(), now.getMonth() - i, 1)
                //const label = d.toISOString().slice(0, 7) // "YYYY-MM"
                const label = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
                months.push(label)
            }

            // Map API data by label for quick lookup
            const salesMap = {}
            monthly_sales.forEach(s => {salesMap[s.label] = s.base_amount || 0 })

            // Build data array aligned with months
            const data = months.map(m => salesMap[m] ?? 0)

            // Assign to chart config
            this.salesTrendSeries = [{ name: "sales", data }];
            this.salesTrendOptions = {
                chart: { id: "sales-trend-chart" },
                xaxis: { categories: months }
            };
        },
        scrollHanle(evt) {
            console.log(evt)
        },
    },
    mounted() {
        this.getAllInitials();
    },
}
</script>