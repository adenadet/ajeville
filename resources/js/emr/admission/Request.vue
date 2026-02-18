<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-md-4">
            <EMRAdmissionDetailRequest :request.sync="request" />
        </div>
        <div class="col-md-8">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="admission-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="patient-tab" data-toggle="pill" href="#patient" role="tab" aria-controls="patient" aria-selected="true">Patient</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="bed-assignment-tab" data-toggle="pill" href="#bed-assignment" role="tab" aria-controls="bed-assignment" aria-selected="false">Bed Assignment</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="admission-prechecks-tab" data-toggle="pill" href="#admission-prechecks" role="tab" aria-controls="admission-prechecks" aria-selected="false">PreChecks</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="admission-tabContent">
                        <div class="tab-pane fade show active" id="patient" role="tabpanel" aria-labelledby="patient-tab">
                            <EMRPatientDetailCard :patient.sync="request.patient" />
                        </div>
                        <div class="tab-pane fade" id="admission-prechecks" role="tabpanel" aria-labelledby="admission-prechecks-tab">
                            <EMRAdmissionDetailPrechecks :prechecks="request.pre_admission_checks" />
                        </div>
                        <div class="tab-pane fade" id="bed-assignment" role="tabpanel" aria-labelledby="bed-assignment-tab">
                            <EMRAdmissionDetailBedAssignment :bed_assignment.sync="request.bed_assignment" :request.sync="request"/>
                        </div>
                    </div>
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
            current_page: 1,
            departments: [],
            editMode: false,
            loading: false,
            query: '',
            request: {},
            room_types: {data: [], total: 0},
            service_types: [],
            specialties: [],
            statuses: ['pending', 'confirmed', 'checked_in', 'completed', 'cancelled', 'no_show'],
            type: 'active',
        }
    },
    methods: {
        getAllInitials(){
            this.loading = true
            axios.get('/api/emr/admissions/requests/'+this.$route.params.id)
            .then(res => {
                this.request = res.data.request;
                this.$store.dispatch('setPatientCookie', res.data.request.patient);
            })
            .finally(() => {
                this.loading = false
            })
        },
    },
    mounted() {
        this.getAllInitials()
    },
    watch: {
        filters: {
            deep: true,
            handler() {
                this.getAllInitials();
            }
        }
    }
}
</script>