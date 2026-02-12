<template>
<section class="overlay-wrapper">
    <div class="row invoice-info">
        <div class="col-sm-4 invoice-col" v-if="consultation.patient != null">
            Patient: 
            <address>
                <strong>{{ patientName(consultation.patient)}}</strong><br>
                Age: {{age(consultation.patient.user.dob)}} years<br>
                Genotype: {{ consultation.patient.genotype != null ? consultation.patient.genotype : 'Not Given'}}<br>
            </address>
        </div>
        <div class="col-sm-4 invoice-col" >
            Consultant:
            <address v-if="consultation.consultant != null">
                <strong>{{ FullName(consultation.consultant) }}</strong><br>
            </address>

        </div>
        <div class="col-sm-4 invoice-col">
            <b>Unique ID: {{ consultation.unique_id }}</b><br>
            <b>Order ID:</b> 4F3S8J<br>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <b>Complaint:</b>
            <blockquote class="quote-secondary" v-html="consultation.complaint"></blockquote>
        </div>
        <div class="col-md-6">
            <b>History:</b>
            <blockquote class="quote-info" v-html="consultation.history"></blockquote>
        </div>
    </div>
    <div class="row">
        <div class="col-12" v-if="consultation.plan != null">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Plan</h3>
                </div>
                <div class="card-body table-responsive p-0 pb-0">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td>Treatment Plan</td><td colspan="7" v-html="consultation.plan.plan"></td>
                            </tr>
                            <tr>
                                <td>Non Drug Interventions</td><td colspan="8" v-html="consultation.plan.non_drug"></td>
                            </tr> 
                            <tr>
                                <td colspan="2">Follow Up Date</td><td colspan="2" v-html="consultation.plan.follow_up_date"></td>
                                <td>Notes:</td><td colspan="4" v-html="consultation.plan.follow_up_note"></td>
                            </tr> 
                            <tr>
                                <td colspan="5"><span v-if="consultation.plan.intent.admission">Admission Adviced</span></td>
                                <td colspan="5"><span v-if="consultation.plan.intent.referral">Referral Issued</span></td>
                            </tr>   
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row" v-if="consultation.requests != null">
        <div class="col-12 mb-3">
            <b class="">Further Treatment</b>
        </div>
        <div class="col-md-6">
            <div class="card" v-if="consultation.requests.laboratory != null && consultation.requests.laboratory.length > 0">
                <div class="card-header bg-dark"><h3 class="card-title">Laboratory Investigations</h3></div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name </th>
                                <th>Category </th>
                                <th>Quantity </th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in consultation.requests.laboratory"
                                :key="item.id">
                                <td>{{ item.name }}</td>
                                <td>{{ item.category != null ? item.category.name : 'Not applicable' }}</td>
                                <td><input class="form-control" type="number" v-model="modelValue[index].quantity" /></td>
                                <td><textarea class="form-control" v-model="modelValue[index].description"></textarea></td>
                                <td><button class="btn btn-xs btn-danger" type="button" @click="removeLaboratoryItem(index)"><i class="fa fa-trash"></i></button> </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card" v-if="consultation.requests.radiology != null && consultation.requests.radiology.length > 0">
                <div class="card-header bg-dark"><h3 class="card-title">Radiological Investigations</h3></div>
                <div class="card-body p-0">
                    <table class="table table-striped" v-if="consultation.requests.radiology != null && consultation.requests.length > 0">
                        <thead>
                            <tr>
                                <th>Name </th>
                                <th>Category </th>
                                <th>Quantity </th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in consultation.requests.radiology"
                                :key="item.id">
                                <td>{{ item.name }}</td>
                                <td>{{ item.category != null ? item.category.name : 'Not applicable' }}</td>
                                <td><input class="form-control" type="number" v-model="modelValue[index].quantity" /></td>
                                <td><textarea class="form-control" v-model="modelValue[index].description"></textarea></td>
                                <td><button class="btn btn-xs btn-danger" type="button" @click="removeLaboratoryItem(index)"><i class="fa fa-trash"></i></button> </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12" v-if="consultation.requests.prescription != null && consultation.requests.prescription.length > 0">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Prescriptions</h3>
                </div>
                <div class="card-body table-responsive p-0 pb-0">
                    <table class="table table-striped text-nowrap">
                        <thead class="">
                            <tr>
                                <th>#</th>
                                <th>Drug</th>
                                <th>Specific Drug</th>
                                <th>Dose</th>
                                <th>Total Qty</th>
                                <th>Form</th>
                                <th>Duration</th>
                                <th>Freq</th>
                                <th>Route</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(drug, index) in consultation.requests.prescription" :key="index">
                                <td>{{ index + 1 }}</td>
                                <td>{{ drug.drug_name }}</td>
                                <td>{{ drug.specific_drug?.name || '' }}</td>
                                <td>{{ drug.dose }}</td>
                                <td>{{ drug.total_quantity }}</td>
                                <td>{{ drug.drug_form }}</td>
                                <td>{{ drug.duration }}</td>
                                <td>{{ drug.frequency }}</td>
                                <td>{{ drug.route }}</td>
                                <td v-html="drug.detail"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <p class="lead">Payment Methods:</p>

            <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
            Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem
            plugg
            dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
            </p>
        </div>
        <div class="col-6">
            <p class="lead">Amount Due 2/22/2014</p>

            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        <tr>
                            <td style="width:50%">Subtotal:</td>
                            <td>$250.30</td>
                        </tr>
                        <tr>
                            <td>Tax (9.3%)</td>
                            <td>$10.34</td>
                        </tr>
                        <tr>
                            <td>Shipping:</td>
                            <td>$5.80</td>
                        </tr>
                        <tr>
                            <td>Total:</td>
                            <td>$265.24</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section> 
</template>
<script>
export default {
    computed: {
        consultation: {
            get() { return this.modelValue },
            set(v) { this.$emit('update:modelValue', v) }
        }
    },
    data() {
        return {
            dialysis_types: [],
            loading: false,
        }
    },
    emits: ['update:modelValue'],
    methods:{
        getAllInitials(){
            //Get the different types pf Dialysis officially
        },
    },
    mounted(){
        this.getAllInitials();
    },
    props: {
        modelValue: {
            type: [Object, Array],
            default: () => ({}),
        },
    },
}
</script>