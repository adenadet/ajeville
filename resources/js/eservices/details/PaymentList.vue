<template>
    <div class="card-header bg-navy">
        <h3 class="card-title">All Payments</h3>
        <div class="card-tools">
            <button class="btn btn-sm btn-primary" @click="addPayment"><i class="fa fa-calendar-plus"></i> Make Payment</button>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Applicant</th>
                    <th>Service </th>
                    <th>Amount</th>
                    <th>Channel</th>
                    <th>By</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="payments.data == null || payments == null || payments.data.length == 0">
                <tr>
                    <td colspan="7" class="text-center">You have not made any payments yet</td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr v-for="payment in payments.data" :key="payment.id">
                    <td>{{ExcelDateShort(payment.created_at) }}</td>
                    <td>{{FullName(payment.patient)}}</td>
                    <td>{{payment.service_id != null && payment.service != null ? payment.service.name : ''}}</td>
                    <td>{{currency(payment.amount)}}</td>
                    <td>{{payment.channel}} <br /><small>{{payment.details}}</small></td>
                    <td><span class="tag tag-success">{{payment.employee_id != null && payment.employee != null ? payment.employee.first_name+' '+payment.employee.last_name:'Old Staff'}}</span></td>
                    <td>
                        <!--<div class="btn btn-group">
                            <router-link :to="'/eservices/front_office/payment/'+payment.id"><button class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></button></router-link>
                            <button class="btn btn-success btn-sm"><i class="fa fa-file-pdf"></i></button>
                            <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                        </div>--> 
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
export default {
    data() {
        return {
            payment: {},
            unpaid_apppointments: {},
            editMode: true,
        }
    },
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        addPayment(){
            this.$Progress.start();
            this.paySpecific = false;
            //this.unpaid_appointments;
            Fire.$emit('UnpaidAppointments', this.unpaid_apppointments);
            $('#paymentModal').modal('show');
            this.$Progress.finish();
        },
        getAllInitials() {
            axios.get('/api/emr/payments').then(response => {
                this.refreshPayments(response)
                })
                .catch(() => {
                    this.$Progress.fail();
                    toast.fire({
                        icon: 'error',
                        title: 'Your payments did not loaded successfully',
                    })
                });
        },
        getPayments(page=1){
            axios.get('/api/emr/payments?page='+page)
            .then(response=>{this.payments = response.data.payments;});
        },
        refreshPayments(response) {
            this.payments = response.data.payments;
            this.unpaid_apppointments = response.data.unpaid_appointments;
        },
    },
    props: {
        payments: {},
        source: String,
    },
    watch:{
        payments(){

        }
    }
}
</script>