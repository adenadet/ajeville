<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="invoice p-3 mb-3">
                        <div class="row">
                            <div class="col-12">
                                <h4>
                                    <i class="fas fa-globe"></i>
                                    <small class="float-right">Date: {{ today | excelDate }}</small>
                                </h4>
                            </div>
                        </div>
                        <div class="row invoice-info">
                            <div class="col-sm-4 invoice-col">
                            From
                            <address>
                                <strong>St. Nicholas Hospital</strong><br>
                                {{ visit.branch != null ? visit.branch.address: ''}} <br>
                                Phone: {{ visit.branch != null ?  visit.branch.phone: ''}}<br>
                                Email: {{ visit.branch != null ?  visit.branch.email: ''}}
                            </address>
                        </div>
                        <div class="col-sm-4 invoice-col">
                            To
                            <address v-if="visit.patient != null">
                                <strong>{{visit.patient.user | FullName}}</strong><br>
                                {{ visit.patient.unique_id }}<br>
                                {{visit.patient.address}}<br>
                                Phone: {{ visit.patient.phone }}<br>
                                Email: {{ visit.patient.email }}<br />
                            </address>
                        </div>
                        <div class="col-sm-4 invoice-col">
                            <b>Visit ID: {{ visit.unique_id }}</b><br>
                            <b>Visit Start Date:</b> {{ visit.start_date | excelDate }}<br>
                            <b>Visit End Date:</b> {{ visit.end_date != null ? ( visit.end_date | excelDate) : 'Ongoing' }}<br>
                            <b>Visit Payment Partner:</b> {{visit.partner != null ? (visit.partner.provider != null ? visit.partner.provider.name+' | '+visit.partner.name : visit.partner.name) : 'None'   }}
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Date</th>
                                        <th>Qty</th>
                                        <th>Item Name</th>
                                        <th>Service Type</th>
                                        <th>Unit Price</th>
                                        <th>Discount</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(transaction, index) in transactions" :key="transaction.id">
                                        <td>{{ index | addOne }}</td>
                                        <td>{{ transaction.date }}</td>
                                        <td>{{ transaction.item_qty }}</td>
                                        <td>{{ transaction.item_name }}</td>
                                        <td>{{ transaction.service_type.name }}</td>
                                        <td>{{ transaction.item_unit_cost | currency }}</td>
                                        <td v-if="transaction.item_discount != null">{{  transaction.item_discount | currency}}</td>
                                        <td v-else>0.00</td>
                                        <td>{{ (transaction.item_total - transaction.discount)  | currency}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                                <span>Amount in Words: <b>{{ (Number(sumOfTransactions) + Number(visit.tax != null ? visit.tax: 0.00) + Number(visit.other_charges != null ? visit.other_charges : 0.00)) | convertNumber2Words }} Naira Only </b></span>
                            </p>
                        </div>
                        <div class="col-6">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td style="width:50%">Subtotal:</td>
                                            <td>{{ currency(sumOfTransactions) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tax </td>
                                            <td>{{ currency(visit.tax) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Other Charges:</td>
                                            <td>{{ currency(visit.other_charges) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total:</td>
                                            <td>{{ cuurency((Number(sumOfTransactions) + Number(visit.tax != null ? visit.tax: 0.00) + Number(visit.other_charges != null ? visit.other_charges : 0.00))) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    </section>
</template>
<script>
import { ModelListSelect } from 'vue-search-select';

export default {
    computed: {
        sumOfTransactions() {
            if (this.transactions.length == 0){
                return 0;
            }
            return this.transactions.reduce((sum, transaction) => {
                return sum += (transaction.item_total - transaction.discount);
            }, 0);
        },
        today(){
            return new Date().toDateString();
        }
    },
    components: {
        ModelListSelect
    },
    data() {
        return {
            visit: {},
            transactions: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials(page=1) {
            this.$Progress.start();
            axios.get('/api/emr/hims/visits/bills/'+this.$route.params.id).then(response => {
                this.refresh(response);
                this.$Progress.finish();
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Visit Form was not loaded successfully',
                })
            });
        },
        removeItem(index){
            alert(index);
            this.InvestigationForm.investigations.splice(index, 1);
        },
        sortStaff(){},
        refresh(response) {
            this.transactions = response.data.transactions;
            this.visit = response.data.visit;
        },
    },
    props: {
        patient: Object,
        editMode: Boolean,
    }
}
</script>