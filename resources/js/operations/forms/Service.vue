<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading">
        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
        <div class="text-bold pt-2">Loading...</div>
    </div>

    <form @submit.prevent="editMode ? updateService() : createService()">
        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Service Name</label>
                <input v-model="serviceData.item.name" type="text" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Service Type</label>
                <select v-model="serviceData.item.service_type_id" class="form-control" required>
                    <option value="">-- Select --</option>
                    <option v-for="t in service_types" :key="t.id" :value="t.id">
                        {{ firstUp(t.name) }}
                    </option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select v-model="serviceData.item.status" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Landing Cost</label>
                <input type="number" step="0.01" v-model.number="serviceData.item.landing_cost" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Billable</label>
                <select v-model="serviceData.item.billable" class="form-control" required>
                    <option value="">-- Select --</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Barcode</label>
                <input v-model="serviceData.item.barcode" class="form-control">
            </div>
        </div>
        <component v-if="currentComponent" :is="currentComponent" v-model="serviceData.service"/>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <QuillEditor v-model:content="serviceData.item.description" content-type="html" theme="snow"/>
                </div>
            </div>
        </div>

        <button class="btn btn-primary mt-3" :disabled="loading"><span v-if="loading" class="spinner-border spinner-border-sm"></span>Save </button>
    </form>
</section>
</template>


<script>
import ConsultationServiceForm from '../components/ConsultationService.vue'
import AdmissionServiceForm from '../components/AdmissionService.vue'
import LaboratoryServiceForm from '../components/LaboratoryService.vue'
import RadiologyServiceForm from '../components/RadiologyService.vue'
import PhysiotherapyServiceForm from '../components/PhysiotherapyService.vue'
import DialysisServiceForm from '../components/DialysisService.vue'

export default {
    components: {
        ConsultationServiceForm,
        AdmissionServiceForm,
        LaboratoryServiceForm,
        RadiologyServiceForm,
        PhysiotherapyServiceForm,
        DialysisServiceForm,
    },
    computed: {
        currentComponent() {
            const map = {
                4: 'ConsultationServiceForm',
                6: 'LaboratoryServiceForm',
                7: 'RadiologyServiceForm',
                3: 'AdmissionServiceForm',
                8: 'PhysiotherapyServiceForm',
                9: 'ProcedureServiceForm',
                14: 'DialysisServiceForm',
            }
            return map[this.serviceData.item.service_type_id] || null
        }
    },
    data() {
        return {
            loading: false,
            serviceData: new Form({
                id: '',
                item: {
                    barcode: '',
                    billable: '',
                    description: '',
                    landing_cost: '',
                    name: '',
                    service_type_id: '',
                    status: 1,
                },
                service: {}
            }),
            service_types: [
                { id: 3, name: 'Admission' },
                { id: 4, name: 'Consultation' },
                { id: 5, name: 'Registration' },
                { id: 6, name: 'laboratory' },
                { id: 7, name: 'radiology' },
                { id: 8, name: 'physiotherapy' },
                { id: 9, name: 'Procedures' },
                { id: 10, name: 'Nurses' },
                { id: 14, name: 'dialysis' },
            ],
        }
    },
    props: {
        service: Object,
        editMode: Boolean
    },
    watch: {
        service() {
            if (this.service) {
                this.serviceData.fill(this.service)
            }
        },
        'serviceData.item.service_type_id'() {
            // Reset service config when type changes
            this.serviceData.service = {}
        }
    },

    methods: {
        createService(){
            this.loading = true;
            this.serviceData.post('/api/operations/services')
            .then(response => {
                this.$emit('refreshServiceForm');
                this.$swal.fire({icon: 'success', title: 'Service Created Successfully', showConfirmButton: false, timer: 1500});
            })
            .catch(error => {
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                if (error.response.status === 422) {this.serviceData.errors = error.response.data.errors;}
            })
            .finally(() => {
                this.loading = false;
            });
        },
        filterConsultants() {
            this.filteredConsultants = []

            if (this.modelValue.specialty_id == '') {
                this.filteredConsultants = this.consultants;
            }
            else{
                const specialty = this.specialties.find(s => s.id === this.modelValue.specialty_id)
                if (!specialty || !specialty.doctors) {this.filteredConsultants = []; return;}
                // Extract ONLY the user objects
                this.filteredConsultants = specialty.doctors.filter(d => d.user).map(d => d.user)        // safety check
            }
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/operations/services/initials')
            .then(res =>  {
                this.consultants = res.data.consultants;
                this.specialties = res.data.specialties;
            })
        },
        submit() {
            this.loading = true

            const url = this.editMode
                ? `/api/operations/services/${this.serviceData.id}`
                : '/api/operations/services'

            const method = this.editMode ? 'put' : 'post'

            axios[method](url, this.serviceData)
                .then(() => {
                    this.$toast.success('Service saved successfully')
                    this.$router.push('/emr/operations/services')
                })
                .finally(() => {
                    this.loading = false
                })
        },
        updateService(){
            this.loading = true;
            this.serviceData.post('/api/operations/services/'+this.serviceData.id)
            .then(response => {
                this.$emit('refreshServiceForm');
                this.$swal.fire({icon: 'success', title: 'Service updated Successfully', showConfirmButton: false, timer: 1500});
            })
            .catch(error => {
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                if (error.response.status === 422) {this.serviceData.errors = error.response.data.errors;}
            })
            .finally(() => {
                this.loading = false;
            });
        },
        
    }
}
</script>
