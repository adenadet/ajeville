<template>
<section class="overlay-wrapper p-0">
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">
                Pre-Admission Clinical Assessment
            </h3>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs">
                <li class="nav-item" v-for="tab in tabs" :key="tab.key">
                    <a class="nav-link"
                    :class="{active: activeTab === tab.key}"
                    href="#"
                    @click.prevent="activeTab = tab.key">
                        {{ tab.label }}
                    </a>
                </li>
            </ul>

            <div class="mt-3">
                <WaterlowForm v-if="activeTab === 'waterlow'" @update="updatePayload('WATERLOW', $event)" />

                <MustScoreForm v-if="activeTab === 'must'" @update="updatePayload('MUST', $event)" />

                <BedRailRiskForm v-if="activeTab === 'bedrail'" @update="updatePayload('BED_RAIL', $event)" />

                <CannulaForm v-if="activeTab === 'cannula'" @update="updatePayload('CANNULA', $event)" />

                <!--PostFallRiskForm v-if="activeTab === 'postfall'" @update="updatePayload('POST_FALL', $event)" />

                <FallRiskForm v-if="activeTab === 'fallrisk'" @update="updatePayload('FALL_RISK', $event)" /-->
            </div>

        </div>

        <div class="card-footer text-right">
            <button class="btn btn-success" :disabled="!allCompleted || submitting"@click="submitAll">
                <i v-if="submitting" class="fas fa-spinner fa-spin"></i> Complete All Assessments
            </button>
        </div>

    </div>
</section>
</template>
<script>
import WaterlowForm from '../components/WaterlowForm.vue'
import MustScoreForm from '../components/MustScoreForm.vue'
import BedRailRiskForm from '../components/BedRailRiskForm.vue'
import CannulaForm from '../components/CannulaForm.vue'
import PostFallRiskForm from '../components/PostFallRiskForm.vue'
import FallRiskForm from '../components/FallRiskForm.vue'

export default {
    components: {
        BedRailRiskForm, CannulaForm, FallRiskForm, MustScoreForm, PostFallRiskForm, WaterlowForm,
    },
    computed: {
        allCompleted() {
            return Object.keys(this.payloads).length === 4;
        }
    },
    data() {
        return {
            precheckData: new Form({
                admission_id: '',
                prechecks:[],
            }),
            activeTab: 'waterlow',
            submitting: false,
            tabs: [
                { key: 'waterlow', label: 'Waterlow' },
                { key: 'must', label: 'MUST Score' },
                { key: 'bedrail', label: 'Bed Rail' },
                { key: 'cannula', label: 'Cannula' },
                //{ key: 'postfall', label: 'Post Fall' },
                //{ key: 'fallrisk', label: 'Fall Risk' }
            ],
            payloads: {

            }
        }
    },
    emits:['refreshRequestForm'],
    
    methods: {

        updatePayload(code, data) {
            this.payloads[code] = data;
        },

        submitAll() {

            if (!this.allCompleted) return;

            this.submitting = true;

            const checks = Object.keys(this.payloads).map(code => ({
                code: code,
                name: this.payloads[code].name,
                result: this.payloads[code].result,
                notes: this.payloads[code].notes,
                meta: this.payloads[code].meta
            }));

            axios.put(
                `/api/emr/admissions/requests/${this.admission.id}/prechecks`,
                { checks }
            )
            .then(() => {
                this.$emit('completed');
            })
            .finally(() => {
                this.submitting = false;
            });
        },
        createRequest(){
            this.loading = true;
            this.requestData.post('/api/emr/admissions/requests')
            .then(response => {
                this.$swal.fire({ icon: 'success', title: 'The Request has been created', showConfirmButton: false, timer: 1500 });
                this.$emit('refreshRequestForm');
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            })
            .finally(() => {
                this.loading = false;
            });
        },
        getInitials(){
            axios.get('/api/emr/admissions/requests/initials')
            .then((response) => {
                this.admission_reasons = response.data.reasons;
                this.admission_types = response.data.types;
                this.consultants = response.data.consultants;
                this.wards = response.data.wards;
                this.visits = response.data.visits;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Request Form was loaded successfully',
                })
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Request Form was not loaded successfully',
                })
            });
        },
        updateRequest(){
            this.loading = true;
            this.requestData.put('/api/emr/admissions/requests/'+this.requestData.id)
            .then(response => {
                this.$swal.fire({ icon: 'success', title: 'The Request has been created', showConfirmButton: false, timer: 1500 });
                this.$emit('refreshRequestForm');
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            })
            .finally(() => {
                this.loading = false;
            });
        },
    },
    mounted() {
        this.getInitials();
    },
    props: {
        admission: {type: Object, default: null},
        editMode: {type: Boolean,default: false},
        prechecks: {type:Array, default: []},
    },
    watch:{}
}
</script>