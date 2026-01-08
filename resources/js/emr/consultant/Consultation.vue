<template>
<section class="overlay-wrapper container-fluid">
    <div class="overlay dark" v-if="loading">
        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
        <div class="text-bold pt-2">Loading...</div>
    </div>

    <div class="col-md-12">
        <div id="accordion">
            <!-- Visit Details -->
            <div class="card card-dark">
                <div class="card-header">
                    <h4 class="card-title">
                        <a data-toggle="collapse" href="#visitDetails">
                            Visit Detail
                        </a>
                    </h4>
                </div>
                <div id="visitDetails" class="collapse show">
                    <div class="card-body">
                        <EMRFrontOfficeDetailVisit source="consultation"/>
                    </div>
                </div>
            </div>

            <!-- Consultation -->
            <div class="card">
                <div class="card-header bg-navy">
                    <h4 class="card-title">Consultation Form</h4>
                </div>

                <div class="card-body">
                    <EMRConsultantFormConsultant v-model="consultation" :visit="visit" :patient="patient" @submitted="reloadConsultation"/>
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
            loading: false,
            consultation: this.emptyConsultation(),
            patient: {},
            visit: {},
        };
    },

    methods: {
        emptyConsultation() {
            return {
                id: this.$route.params.id,
                complaint: '',
                history: '',
                initial_icd_10: [],
                final_icd_10: [],
                action_plan: '',
                plan: {},
                requests: {
                    admission: [],
                    dialysis: [],
                    laboratory: [],
                    radiology: [],
                    prescriptions: [],
                    physiotherapy: [],
                }
            };
        },
        reloadConsultation() {
            this.loading = true;
            axios.get(`/api/emr/consultations/consultants/${this.$route.params.id}`)
            .then(response => {
                this.consultation = response.data.consultation;
                this.consultation.requests.admission = response.data.consultation.admission;
                this.consultation.requests.dialysis = response.data.consultation.dialysis;
                this.consultation.requests.laboratory = response.data.consultation.laboratory;
                this.consultation.requests.radiology = response.data.consultation.radiology;
                this.consultation.requests.prescriptions = response.data.consultation.prescriptions;
                this.consultation.requests.physiotherapy = response.data.consultation.physiotherapy;
                this.patient = response.data.consultation.patient;
                this.visit = response.data.consultation.visit;
            })
            .finally(() => {
                this.loading = false;
            });
        }
    },

    mounted() {
        this.reloadConsultation();
    }
};
</script>
