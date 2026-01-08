<template>
<section class="container-fluid">
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cog"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">CPU Traffic</span>
                    <span class="info-box-number">10</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Likes</span>
                    <span class="info-box-number">41,410</span>
                </div>
            </div>
        </div>
        <div class="clearfix hidden-md-up"></div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Sales</span>
                    <span class="info-box-number">760</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">New Members</span>
                    <span class="info-box-number">2,000</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner"><h3>150</h3><p>New Orders</p></div>
                <div class="icon"><i class="ion ion-bag"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner"><h3>53</h3><p>Bounce Rate</p></div>
                <div class="icon"><i class="ion ion-stats-bars"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner"><h3>44</h3><p>User Registrations</p></div>
                <div class="icon"><i class="ion ion-person-add"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner"><h3>65</h3><p>Unique Visitors</p></div>
                <div class="icon"><i class="ion ion-pie-graph"></i></div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pending Payments</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Invoice ID</th>
                                <th>Vendor</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th style="width: 15px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(invoice, index) in pending_invoices.data">
                                <td>{{ index | addOne }}</td>
                                <td><router-link :to="'/finance/invoices/'+invoice.id">{{ invoice.unique_id }}</router-link></td>
                                <td>{{ invoice.vendor.name }}</td>
                                <td>{{ invoice.vendor.amount }}</td>
                                <td>{{ invoice.due_date }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Overdue Payments</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Invoice ID</th>
                                <th>Vendor</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th style="width: 15px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(invoice, index) in overdue_invoices.data">
                                <td>{{ index | addOne }}</td>
                                <td><router-link :to="'/finance/invoices/'+invoice.id">{{ invoice.unique_id }}</router-link></td>
                                <td>{{ invoice.vendor.name }}</td>
                                <td>{{ invoice.vendor.amount }}</td>
                                <td>{{ invoice.due_date }}</td>
                                <td></td>
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
            active_visits: 0,
            invoices: {},
            patients: [],
            overdue_invoices: {},
            pending_invoices: {},

        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials() {
            axios.get('/api/emr/hims/dashboard').then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        makePayment(appointment){
            this.$Progress.start();
            this.paySpecific = true;
            Fire.$emit('PaymentDataFill', appointment);
            $('#paymentModal').modal('show');
            this.$Progress.finish();
        },
        refreshPage(response) {
            this.active_visits = response.data.active_visits;
            this.patients = response.data.patients;
        }
    },
    props: {}
}
</script>