<template>
<section class="overlay-wrapper p-0">
    <div class="invoice p-3 mb-3" id="summary-bill">
        <div class="row">
            <div class="col-12">
                <h4><i class="fas fa-hospital"></i> {{ facility.name }} <small class="float-right">Date: {{ today }}</small></h4>
            </div>
        </div>
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                From
                <address>
                    <strong>{{ facility.name }}</strong><br>
                    {{ facility.address }}<br>
                    Phone: {{ facility.phone }}<br>
                    Email: {{ facility.email }}
                </address>
            </div>
            <div class="col-sm-4 invoice-col">
                To
                <address>
                    <strong>{{ patientName(visit.patient) }}</strong><br>
                    {{ visit.patient.address }}<br>
                    Phone: {{ visit.patient.phone }}<br>
                    Email: {{ visit.patient.email }}
                </address>
            </div>
            <div class="col-sm-4 invoice-col">
                <b>Visit #{{ visit.visit_no }}</b><br>
                <b>Invoice ID:</b> {{ invoice_no }}<br>
                <b>Status:</b> <span class="badge bg-success">PAID</span>
            </div>
        </div>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Service Type</th>
                            <th>Total Items</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(group, index) in summaryGrouped" :key="group.service_type_id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ group.service_type_name }}</td>
                            <td>{{ group.count }}</td>
                            <td>{{ currency(group.total_amount) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totals -->
        <div class="row">
            <div class="col-6"></div>
            <div class="col-6">
            <div class="table-responsive">
                <table class="table">
                <tr>
                    <th style="width:50%">Grand Total:</th>
                    <td>{{ currency(grandTotal) }}</td>
                </tr>
                </table>
            </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <h5>Patient Confirmation</h5>
                <VueSignaturePad ref="signaturePad" :options="{ penColor: 'black' }" width="100%" height="200px"/>

                <button class="btn btn-danger mt-2" @click="clearSignature">
                    Clear Signature
                </button>
            </div>
        </div>
        <div class="row no-print mt-3">
            <div class="col-12">
                <button class="btn btn-primary" @click="printBill">
                    <i class="fas fa-print"></i> Print
                </button>

                <button class="btn btn-success float-right" @click="confirmBill">
                    Confirm & Sign
                </button>
            </div>
        </div>
    </div>
</section>
</template>