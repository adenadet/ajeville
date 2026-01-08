<template>
<section class="overlay-wrapper p-0">
<form @submit.prevent="submit">
    {{ consultationData }}
    <!-- Draft Controls -->
    <div class="row">
        <div class="d-flex justify-content-between mb-2">
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary me-2" @click="saveDraft(true)">Save Draft</button>
                <button v-for="draft in drafts" :key="draft.version" type="button" class="btn btn-sm btn-outline-info me-1" @click="restoreDraft(draft.version)">Draft {{ draft.version }}</button>
            </div>

            <small class="text-muted" v-if="lastSavedAt">
                Autosaved {{ lastSavedAt }}
            </small>
        </div>
        {{ consultationData.patient.user }}
        <div class="col-md-12"><Complaint v-model="consultationData.complaint" :durations.sync="durations" :symptoms.sync="symptoms"/></div>
        <div class="col-md-6"><Diagnosis v-model="consultationData.initial_icd_10" :icd_10_codes="icd_10_codes" type="initial"/></div>
        <div class="col-md-6"><Diagnosis v-model="consultationData.final_icd_10" :icd_10_codes="icd_10_codes" type="final" /></div>
        <div class="col-md-12"><Plan v-model="consultationData.plan" /></div>
        <div class="col-md-12"><Requests v-model="consultationData.requests" /></div>
    </div>
    <div class="row" v-if="review == true"><ConsultationReview v-model="consultationData" @back="review=false" @confirm="submit"/></div>

    <div class="mt-3">
        <button class="btn btn-secondary me-2" v-if="review == false" @click="review=true">Review</button>
        <button class="btn btn-secondary me-2" v-if="review == true" @click="review=false">Back</button>
        <button class="btn btn-primary float-right" @click="submit">Submit</button>
    </div>

</form>
</section>
</template>

<script>
import debounce from 'lodash/debounce';
import Complaint from '../components/Complaint.vue';
import ConsultationReview from '../components/ConsultationReview.vue';
import History from '../components/History.vue';
import Diagnosis from '../components/Diagnosis.vue';
import Plan from '../components/Plan.vue';
import Requests from '../components/Requests.vue';

export default {
    components:{
        Complaint, ConsultationReview, History, Diagnosis, Plan, Requests
    },
    computed: {
        consultationData: {
            get() {return this.modelValue;},
            set(val) {this.$emit('update:modelValue', val);}
        },
        draftStorageKey() {
            return `consultation-drafts-${this.consultationData.id}`;
        }
    },
    created() {
        this.debouncedAutosave = debounce(() => {this.saveDraft(false);}, 1500);
    },
    data() {
        return {
            drafts: [],
            lastSavedAt: null,
            review: false,
            durations: [],
            symptoms: [],
            icd_10_codes: []
        };
    },
    emits: ['update:modelValue', 'submitted'],
    methods: {
        getInitials() {
            axios.get('/api/emr/consultations/consultants/initials')
            .then((response) => {
                this.refreshPage(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Consultation Form was loaded successfully',
                })
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Consultation Form was not loaded successfully',
                })
            });
        },
        saveDraft(manual = false) {
            const drafts = this.getDrafts();
            const version = drafts.length + 1;

            drafts.push({
                version,
                saved_at: new Date().toISOString(),
                data: JSON.parse(JSON.stringify(this.consultationData))
            });

            localStorage.setItem(this.draftStorageKey, JSON.stringify(drafts));

            this.drafts = drafts;
            this.lastSavedAt = manual ? 'just now' : 'a moment ago';
        },
        refreshPage(response) {
            this.durations = response.data.durations;
            this.frequencies = response.data.frequencies;
            this.icd_10_codes = response.data.icd_10_codes;
            this.locations = response.data.locations;
            this.positions = response.data.positions;
            this.routes = response.data.routes;
            this.symptoms = response.data.symptoms;
        },
        restoreDraft(version) {
            const draft = this.drafts.find(d => d.version === version);
            if (!draft) return;

            this.$emit('update:modelValue', {
                ...this.consultationData,
                ...draft.data
            });

            this.$toast.fire({
                icon: 'info',
                title: `Draft ${version} restored`
            });
        },
        getDrafts() {
            return JSON.parse(localStorage.getItem(this.draftStorageKey) || '[]');
        },
        submit() {
            axios.put(
                    `/api/emr/consultations/consultants/${this.consultationData.id}`,
                    this.consultationData
                )
                .then(() => {
                    localStorage.removeItem(this.draftStorageKey);
                    this.$emit('submitted');
                });
        }
    },
    mounted() {
        this.getInitials();
        this.drafts = this.getDrafts();
    },
    props: {
        modelValue: { type: Object, required: true }
    },
    watch: {
        consultationData: {
            deep: true,
            handler() {
                this.debouncedAutosave();
            }
        }
    },
};
</script>
