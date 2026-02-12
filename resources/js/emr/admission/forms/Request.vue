<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateRequest() :createRequest()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Visit</label>
                    <select class="form-control" name="visit_id" id="visit_id" v-model="requestData.visit_id"  v-if="request.visit_id == null">
                        <option value="">--Select Visit--</option>
                        <option v-for="visit in visits" :value="visit.id">{{ visit.unique_id }} - {{ patientName(visit?.patient) }}</option>
                    </select>
                    <div class="form-control">
                        {{ request.visit?.unique_id }} - {{ patientName(request.patient) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Admission Type</label>
                    <select class="form-control" name="admission_type_id" id="admission_type_id" v-model="requestData.admission_type_id">
                        <option value="">--Select Type--</option>
                        <option v-for="admission_type in admission_types" :value="admission_type.id">{{ admission_type.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Admission Reason</label>
                    <select class="form-control" name="admission_reason" id="admission_reason" v-model="requestData.admission_reason">
                        <option value="">--Select Reason--</option>
                        <option v-for="admission_reason in admission_reasons" :value="admission_reason.id">{{ admission_reason.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Requested By</label>
                    <select class="form-control" name="requested_by" id="requested_by" v-model="requestData.requested_by">
                        <option value="">--Select Consultant--</option>
                        <option v-for="consultant in consultants" :value="consultant.id">{{ consultant.first_name+' '+consultant.last_name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Requested Date</label>
                    <input class="form-control" type="date" name="requested_at" id="requested_at" v-model="requestData.requested_at">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Remark</label>
                    <QuillEditor theme="snow" content-type="html" class="form-control" v-model:content="requestData.requested_remark" id="requested_remark" name="requested_remark" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-primary" type="submit">{{ loading ? 'Requesting...' : 'Save' }}</button> 
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default { 
    data() {
        return {
            admission_reasons: [],
            admission_types: [],
            consultants: [],
            loading: false,
            requestData: new Form({
                id: '',
                date:'', 
                visit_id:'',
                patient_id: '', 
                admission_type_id: '', 
                admission_reason:'',
                requested_remark:'',
                requested_by: '',
                requested_at: '',
            }),
            room_types: [],
            visits: [],
            wards: [],

        }
    },
    emits:['refreshRequestForm'],
    methods: {
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
        editMode: {type: Boolean,default: false},
        request: {type: Object, default: null},
    },
    watch:{
        request(){
            this.requestData.fill(this.request);
        },
    }
}
</script>