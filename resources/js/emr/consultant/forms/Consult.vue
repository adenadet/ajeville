<template>
<section class="overlay-wrapper p-0">
    <form @submit.prevent="submit">
        <div class="row">
        <!-- SOAP -->
            <div class="col-md-12">
                <Complaint v-model="consultationData.complaint" :durations.sync="durations" :symptoms.sync="symptoms"/>
            </div>
            <!--div class="col-md-12">
                <History v-model="consultationData.history" :symptoms="symptoms"/>
            </div-->
            <div class="col-md-6">
                <Diagnosis :icd_10_codes="icd_10_codes" v-model="consultationData.initial_icd_10" type="initials" />
            </div>
            <div class="col-md-6">
                <Diagnosis :icd_10_codes="icd_10_codes" v-model="consultationData.final_icd_10" type="final"/>
            </div>
            <div class="col-md-12">
                <Plan v-model="consultationData.action_plan" />
            </div>
            
            <!-- REQUESTS -->
            <div class="col-md-12">
                <Requests v-model="consultationData.requests" />
            </div>
        </div>
        <ConsultationReview v-if="review"  :consultationData="consultationData" @back="review=false" @confirm="submit"/>
    
        <div class="row">
            <button class="btn btn-secondary me-2" @click="review = true">Review</button>
            <button class="btn btn-primary" @click="submit">Submit</button>
        </div>

    </form>
</section>
</template>
<script>
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
            consultationData: {
                ...this.modelValue,
                final_icd_10: [],
                initial_icd_10: [],
                requests: {
                    admission: [],
                    dialysis: [],
                    laboratory: [],
                    radiology: [],
                    prescriptions: [],
                    physiotherapy: [],
                }
            },
            durations: [],
            drugs: [],
            drugName: '',
            drugId: '',
            drugs: '',
            specific_drugs: [],
            modal: false,
            drugForms: [],
            loading: false,
            routes: [], 
            frequencies:[],
            final_complaining: "assisted",
            final_complaining_history: false,
            final_history: false,
            history_type: 'assisted',
            locations: [],
            modal: true,
            radiology_investigation: '',
            radiology_services: [],
            serviceName: '',
            serviceId: '',
            services: [],
            serviceForms: [],
            socrates: {active: 'site',},
            specific_drugs: [],
            symptoms: [],
            icd_10_codes: [],
            itemForm: new Form({ description: '', detail: '', service_id: '', service_name: '', quantity: '', symptoms: [], }),
            investigationForm: new Form({ id: '', doctor_id: '', doctor_name: '', start_date: '', patient_id: '', services: [], }),
            review: false,
            symptoms: [],
            value: [],
        }
    },
    emits: ['update:modelValue', 'submitted'],
    methods: {
        addTag(newTag) {
            const tag = {
                name: newTag,
                code: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
            }
            this.options.push(tag)
            this.value.push(tag)
        },
        addRadiologyItem(){
            var item = this.radiology_services.find(item => item.id === this.radiology_investigation);
            var index = this.consultationForm.radiology.map(function(o) { return o.id; }).indexOf(this.investigation);
            if (index < 0){
                this.consultationForm.radiology.push({id: item.id, category_id:item.category_id, description: '', name: item.name, quantity: 1, service_id:item.service_id,})
            }
            else{
                this.consultationForm.radiology[index].quantity++;
            }
            this.radiology_investigation = '';
        },
        createConsultation() {
            this.consultationForm.id = this.$route.params.id;
            this.consultationForm.post('/api/emr/consultations/consultants')
            .then(response => {
                this.loading = false;
                this.$router.push('/emr/consultations/doctor_queue');
                this.$swal.fire({ icon: 'success', title: 'The Consultation has been saved', showConfirmButton: false, timer: 1500 });
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            });
        },
        generateComplaintNote() {
            var note = '<p>The patient presented complaining of: <ul>';
            var sub_note = '';
            for (let i = 0; i < this.complaintForm.symptoms.length; i++) {
                sub_note = '<li>' + this.complaintForm.symptoms[i].name;
                if (this.complaintForm.symptoms[i].duration != null) {
                    sub_note += ' for ' + this.complaintForm.symptoms[i].number + ' ' + this.complaintForm.symptoms[i].duration + '.';
                }
                if (this.complaintForm.symptoms[i].pain_level != null) {
                    sub_note += ' Patient has a pain level of ' + this.complaintForm.symptoms[i].pain_level + ' on a scale of 1 - 10.';
                }
                if (this.complaintForm.symptoms[i].experience_changes != null) {
                    if (this.complaintForm.symptoms[i].experience_changes == 'yes') {
                        sub_note += ' Patient experiences changes best described as ' + this.complaintForm.symptoms[i].experience_change_character + '.';
                    }
                    else {
                        sub_note += ' Patient experienced no changes overtime.';
                    }
                }
                sub_note = sub_note + '</li>';
                note = note + sub_note;
            }
            note = note + '</ul></p>'
            this.consultationForm.complaint = note;
            this.final_complaining_history = true;
            this.final_complaining = "unassisted";
        },
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
        limitText(count) { return `and ${count} other symptoms` },
        changeSocrates(id) {
            this.socrates.active = id;
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
        removeService(service) { this.investigationForm.services.pop(service); },
        reviewConsultation() {this.review = true;},
        returnConsultation(){this.review = false;},
        searchServices() {
            axios.get('/api/emr/hims/services/search?q=' + this.itemForm.service_name)
            .then((response) => { this.drugs = response.data.services; })
            .catch(() => { });
        },
        submitSymptoms() {
            this.itemForm.symptoms = this.value;
            this.value = [];
        },
        setService(drug) {
            this.itemForm.service_name = service.name;
            this.itemForm.service_id = service.id;
            this.services = [];
            this.modal = false;
        },
        updateInvestigation() {
            this.$Progress.start();
            this.investigationForm.put('/api/emr/hims/consultations/' + this.investigationForm.id)
            .then(response => {
                this.loading = false;
                Fire.$emit('pageReload');
                this.$swal.fire({ icon: 'success', title: 'The Investigation has been updated', showConfirmButton: false, timer: 1500 });
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
                this.$Progress.fail();
            });
        },

    },
    mounted() {
        this.getInitials();
    },
    props: {
        modelValue: {
            type: Object,
            required: true
        },
        patient: Object,
        visit: Object,
    },
    watch:{
        consultationData: {
            deep: true,
            handler(val) {
                this.$emit('update:modelValue', val);
            }
        }
    }
}
</script>
