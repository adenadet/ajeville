<template>
    <section>
        <div class="row overlay-wrapper">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <div class="col-md-12" v-if="appointment != null && appointment.payment != null && appointment.patient != null">
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
                                <strong v-if="appointment.patient != null">{{ FullName(appointment.patient) }}</strong>
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
                            <b>Payment Due:</b> {{ ExcelDateShort(appointment.date) }}<br>
                            <b>Account:</b> {{ appointment.payment.channel }}
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
                                        <td>Payment for {{ appointment.service.name }} for  {{ FullName(appointment.patient) }}</td>
                                        <td>{{ currency(appointment.payment.amount) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
    
                    <div class="row" v-if="appointment.payment != null">
                        <div class="col-6">
                            <p class="lead"><strong>Payment Details:</strong></p>
                            
                            <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                                {{ appointment.payment.channel }} <br />
                                {{ appointment.payment.details }}
                            </p>
                        </div>
                        <div class="col-6">
                            <p class="lead">Payment Date {{ ExcelDate(appointment.payment.created_at)  }}</p>
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
    mounted() {},
    methods: {},
    props: {appointment: Object}
}
</script>