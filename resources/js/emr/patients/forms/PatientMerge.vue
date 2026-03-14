<template>
<section class="content">
    <div class="container-fluid">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Step 1: Select Patients</h3>
            </div>

            <div class="card-body row g-3">

                <div class="col-md-5">
                    <label class="form-label">Source Patient (Duplicate)</label>
                    <input type="text"
                           class="form-control"
                           v-model="sourceSearch"
                           @input="searchPatients('source')"
                           placeholder="Search name / unique id" />

                    <ul v-if="sourceResults.length" class="list-group mt-2">
                        <li class="list-group-item list-group-item-action"
                            v-for="p in sourceResults"
                            :key="p.id"
                            @click="selectPatient('source', p)">
                            {{ p.user?.last_name+', ' || '' }} {{ p.user?.first_name }} {{ p.user?.middle_name }} ({{ p.unique_id }})
                        </li>
                    </ul>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Target Patient (Survivor)</label>
                    <input type="text"
                           class="form-control"
                           v-model="targetSearch"
                           @input="searchPatients('target')"
                           placeholder="Search name / unique id" />

                    <ul v-if="targetResults.length" class="list-group mt-2">
                        <li class="list-group-item list-group-item-action"
                            v-for="p in targetResults"
                            :key="p.id"
                            @click="selectPatient('target', p)">
                            {{ p.user?.last_name+', ' || '' }} {{ p.user?.first_name }} {{ p.user?.middle_name }} ({{ p.unique_id }})
                        </li>
                    </ul>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100"
                            :disabled="!sourcePatient || !targetPatient"
                            @click="previewMerge">
                        Preview
                    </button>
                </div>

            </div>
        </div>

        <!-- STEP 2: COMPARISON -->
        <div v-if="previewData" class="card card-outline card-primary mt-3">
            <div class="card-header">
                <h3 class="card-title">Step 2: Compare Records</h3>
            </div>

            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <PatientCard title="Source Patient" variant="warning" view="plain" :patient="previewData.source" />
                    </div>

                    <div class="col-md-6">
                        <PatientCard title="Target Patient" variant="success" view="plain" :patient="previewData.target" />
                    </div>
                </div>

                <hr>

                <div class="alert alert-danger">
                    <strong>Warning:</strong>
                    This action is irreversible.
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Merge Reason</label>
                            <QuillEditor v-model:content="mergeData.reason" theme="snow" content-type="html" class="form-control" required />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" v-model="mergeData.keepTargetUser">
                            <label class="form-check-label"> Keep Target User Account</label>
                        </div>

                        <button class="btn btn-danger w-100 mt-4" :disabled="!mergeData.reason || loading" @click="confirmMerge">
                            <span v-if="loading" class="spinner-border spinner-border-sm"></span> Confirm Merge
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>
</template>
<script>
import PatientCard from '@/emr/patients/details/Summary.vue'
export default {
    components: {
        PatientCard
    },
    data() {
        return {
            sourceSearch: '',
            targetSearch: '',

            sourceResults: [],
            targetResults: [],

            sourcePatient: null,
            targetPatient: null,

            previewData: null,

            
            loading: false, 
            mergeData: new Form({
                keepTargetUser: false,
                reason: '',
                source_id: '',
                target_id: '',
            }),
        }
    },
    emits:['patientMergeReload'],
    computed: {
        canPreview() {return this.sourcePatient && this.targetPatient},
        canMerge() {return this.reason && !this.loading},
    },

    methods: {
        async searchPatients(type) {
            let query = type === 'source' ? this.sourceSearch : this.targetSearch;
            if (query.length < 3) return
            try {
                const response = await axios.get('/api/emr/hims/patients/search', {params: { q: query }})
                if (type === 'source') {
                    this.sourceResults = response.data.patients
                } 
                else {
                    this.targetResults = response.data.patients
                }
            }
            catch (error) {
                console.error(error)
            }
        },

        selectPatient(type, patient) {
            if (type === 'source') {
                this.sourcePatient = patient
                this.sourceSearch = (patient.user?.last_name+', '+patient.user?.first_name+' '+(patient.user?.middle_name || '') || patient.user?.name)  + ' ('+patient.unique_id+')'  
                this.sourceResults = []
            } 
            else {
                this.targetPatient = patient
                this.targetSearch = (patient.user?.last_name+', '+patient.user?.first_name+' '+(patient.user?.middle_name || '') || patient.user?.name)  + ' ('+patient.unique_id+')'  
                this.targetResults = []
            }
        },

        async previewMerge() {
            try {
                const response = await axios.get('/api/emr/hims/patients/merge_preview',
                    {
                        params: {
                            source_id: this.sourcePatient.id,
                            target_id: this.targetPatient.id
                        }
                    }
                )

                this.previewData = response.data
            } 
            catch (error) {
                alert(error.response?.data?.message || 'Preview failed')
            }
        },

        async confirmMerge() {
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You want to merge these patients?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, merge them!'
            })
            .then((result) => {
                if (result.value) {
                    this.loading = true;
                    this.mergeData.source_id = this.sourcePatient.id;
                    this.mergeData.target_id = this.targetPatient.id;
                    this.mergeData.post('/api/emr/hims/patients/merge',)
                    .then(response => {
                        this.$emit('patientMergeReload', response);
                        this.mergeData.reset();
                        this.sourcePatient = null;
                        this.targetPatient = null;
                        this.$swal.fire('Merged!', 'Patients have been merged.', 'success');
                    })
                    .catch(error => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: error.response?.data?.message || 'Merge failed', footer: '<a href>Why do I have this issue?</a>' });
                    })
                    .finally(()=>{
                        this.loading = false;
                    });
                }
            });
            /*if (!confirm('Are you sure you want to merge these patients?')) {return}
            this.loading = true
            try {
                await axios.post('/api/emr/hims/patients/merge', {
                    source_id: this.sourcePatient.id,
                    target_id: this.targetPatient.id,
                    reason: this.reason,
                    keep_target_user: this.keepTargetUser
                })

                alert('Merge successful')
                window.location.reload()

            } catch (error) {
                alert()
            }
            this.loading = false*/
        }
    },
    props:{
        target: Object, 
    },
    watch:{
        target(){
            if (this.target != null && this.target.id != null){
                this.targetSearch = (this.target.user?.last_name+', '+this.target.user?.first_name+' '+(this.target.user?.middle_name || '') || this.target.user?.name)  + ' ('+this.target.unique_id+')'
                this.targetPatient = this.target;
            }
        }
    },
}
</script>