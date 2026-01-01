<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading">
        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
        <div class="text-bold pt-2">Loading...</div>
    </div>

    <form @submit.prevent="submit">
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
        <component v-if="currentComponent" :is="currentComponent" v-model="serviceData.service"/>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <QuillEditor v-model:content="serviceData.item.description" content-type="html" theme="snow"/>
                </div>
            </div>
        </div>

        <button class="btn btn-primary mt-3" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm"></span>
            Save Service
        </button>
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

    props: {
        service: Object,
        editMode: Boolean
    },

    data() {
        return {
            loading: false,
            serviceData: new Form({
                item: {
                    name: '',
                    service_type_id: '',
                    status: 1,
                    description: '',
                },
                service: {}
            }),

            service_types: [
                { id: 1, name: 'consultation' },
                { id: 2, name: 'laboratory' },
                { id: 3, name: 'radiology' },
                { id: 4, name: 'admission' },
                { id: 5, name: 'physiotherapy' },
                { id: 6, name: 'dialysis' },
            ]
        }
    },

    computed: {
        currentComponent() {
            const map = {
                1: 'ConsultationServiceForm',
                2: 'LaboratoryServiceForm',
                3: 'RadiologyServiceForm',
                4: 'AdmissionServiceForm',
                5: 'PhysiotherapyServiceForm',
                6: 'DialysisServiceForm',
            }
            return map[this.serviceData.item.service_type_id] || null
        }
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
        submit() {
            this.loading = true

            const url = this.editMode
                ? `/api/emr/operations/services/${this.serviceData.id}`
                : '/api/emr/operations/services'

            const method = this.editMode ? 'put' : 'post'

            axios[method](url, this.serviceData)
                .then(() => {
                    this.$toast.success('Service saved successfully')
                    this.$router.push('/emr/operations/services')
                })
                .finally(() => {
                    this.loading = false
                })
        }
    }
}
</script>
