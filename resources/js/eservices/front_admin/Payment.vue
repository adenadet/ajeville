<template>
<section>
    <div class="row overlay-wrapper">
        <div class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="col-md-12" v-if="appointment != null">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4><i class="fas fa-globe"></i> St. Nicholas Hospital
                            <small class="float-right">Date: {{ appointment.payment != null ? appointment.payment.created_at : 'Loading' }}</small>
                        </h4>
                    </div>
                </div>
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        From
                        <address>
                            <strong v-if="appointment.patient != null">{{ appointment.patient | fullName }}</strong>
                            <br>
                            Phone: {{ appointment.patient != null ? appointment.patient.phone : '0000000000' }}<br>
                            Email: {{ appointment.patient != null ? appointment.patient.email : '0000000000' }}
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        To
                        <address>
                            <strong>St. Nicholas Hospital</strong><br>
                            57, Campbell Street,<br>
                            Lagos Island, Lagos, Nigeria<br>
                            Phone: (+234) 0201 2800 820<br>
                            Email: info@saintnicholashospital.com
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <b>Invoice #{{appointment.id}}</b><br>
                        <b>Service:</b> {{ appointment.service != null ? appointment.service.name : 'Loading Service' }}<br />
                        <b>Tracking ID:</b> {{ appointment.unique_id}}<br>
                        <b>Payment Due:</b> {{ appointment.date }}<br>
                        <b>Account:</b> {{ appointment.payment != null ? appointment.payment.channel : 'Loading Channel' }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Qty</th>
                                    <th>Service</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Payment for {{ appointment.service != null ? appointment.service.name : 'Unknown Service' }} for  {{ FullName(appointment.patient)}}</td>
                                    <td>{{ appointment.payment != null ? currency(appointment.payment.amount) : currency(0)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <p class="lead">Payment Details:</p>
                        
                        <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                            {{ appointment.payment != null ? appointment.payment.channel : 'Unknown Channel' }} <br />
                            {{ appointment.payment != null ? appointment.payment.details : 'Unknown Details' }}
                        </p>
                    </div>
                    <div class="col-6">
                        <p class="lead">Payment Date {{ appointment.payment != null ? ExcelDate(appointment.payment.created_at) : '6th September, 2024' }}</p>
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
            user: {},
        }
    },
    mounted() {
        /*Fire.$on('refreshPayment', response => {
            this.refreshAppointments(response);
            this.closeModals();
        });*/
    },
    methods: {
        addApplicant(){
            this.$Progress.start();
            this.editMode = false;
            //this.applicant = {};
            Fire.$emit('ApplicantDataFill', {});
            $('#applicantModal').modal('show');
            this.$Progress.finish();
        },
    },
    props: {appointment: Object}
}
</script>