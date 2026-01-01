<template>
    <section class="overlay-wrapper container-fluid">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div> 
        <div class="col-md-12">
            <div id="accordion">
                <div class="card card-dark">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                            Visit Detail
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="card-body">
                            <EMRVisitDetailSummary source="consultation"/>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-navy">
                        <h4 class="card-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="text-white" >
                            Consultation Form
                            </a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse pt-3">
                        <EMRConsultantFormSoapNote />
                    </div>
                </div>
            </div>
        </div>
        <!--:consultation="consultation" v-if="consultation.status < 4"/>
        <EMRConsultantDetailConsultation :consultation v-else/-->
    </section>
</template>
<script>
export default {
    computed:{
        prescriptionQuantity(){
            let duration = this.itemForm.duration;
            let dose = this.itemForm.dose;
            var freq;
            switch(this.itemForm.frequency) {
                case 'Daily':
                    freq = 1;
                    break;
                case 'Weekly':
                    freq = 1/7;
                    break;
                case 'Monthly':
                    freq = 1/30;
                    break;
                case 'Twice Daily (bd)':
                    freq = 2;
                    break;
                case 'Hourly':
                    freq = 24;
                    break;
                case 'Thrice Daily':
                    freq = 3;
                    break;
                case 'Every 6 hours':
                    freq = 4;
                    break;
                }
            return Number(duration * dose * freq);
        },
    },
    data() {
        return {
            appointment: {},
            consultant: {},
            consultation: {},
            contact: {},
            editMode: false,
            loading: false,
            patient: {},
            visit: {},
        }
    },
    methods: {
        getInitials() {
            axios.get('/api/emr/consultations/consultants/' + this.$route.params.id)
            .then((response) => {
                this.consultation = response.data.consultation;
                this.laboratory_services = response.data.laboratory_services;
                this.radiology_services = response.data.radiology_services;
                this.drugForms = response.data.drug_forms; 
                this.frequencies = response.data.frequencies;
                this.routes = response.data.routes; 
                this.specific_drugs = response.data.specific_drugs;
                this.patient = response.data.consultation.patient;
                this.visit = response.data.consultation.visit;

                this.$store.dispatch('setPatient', this.patient);
                this.$store.dispatch('setVisit', this.visit);
            })
            .catch(() => { });
        },
    },
    mounted() {
        this.getInitials();
    },
    /*props: {
        'appointment': Object,
        'consultant': Object,
        'contact': Object,
        'editMode': Boolean,
        'visit': Object,
    },*/
}
</script>