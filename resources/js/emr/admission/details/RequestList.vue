<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="bedAssignmentFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bed Assignment Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRAdmissionFormBedAssignment :admission.sync="request" :editMode="editMode" @refreshPrecheckForm="closeModals()" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="precheckFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Precheck Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRAdmissionFormPrecheck :admission.sync="request" :editMode="editMode" @refreshPrecheckForm="closeModals()" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="requestFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Admission Request Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRAdmissionFormRequest :request.sync="request" :editMode="editMode" @refreshRequestForm="closeModals()" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="requestViewModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Room Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRAdmissionDetailRequest :request.sync="request"  />
                </div>
            </div>
        </div>
    </div>
    <table class="table table-striped table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Patient</th>
                <th>Visit</th>
                <th>Ward</th>
                <th>Bed</th>
                <th>Description</th>
                <th>Status</th>
                <th><button class="btn btn-primary btn-xs" @click="addRequest()"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody v-if="requests.length > 0">
            <tr v-for="(request, index) in requests" :key="request.id">
                <td>{{ addOne(index) }}</td>
                <td>{{ ExcelDate(request.date) }}</td>
                <td>{{ patientName(request.patient) }}</td>
                <td>{{ request.visit?.unique_id || 'No Visit Attached' }}</td>
                <td>{{ request.bed_assignment?.bed?.ward?.name || 'No Bed Assigned' }}</td>
                <td><span v-if="request.bed_assignment == null">No Bed Assigned</span>
                    <span v-else>{{ request.bed_assignment?.bed?.name+"("+request.bed_assignment?.bed?.room?.name+")" || 'No Bed Assigned' }}</span></td>
                <td class="text-small" :title="request.description" v-html="readMore(request.description, 25, '...')"></td>
                <td>
                    <span v-if="request.status == 0"class="badge badge-dark">Pending</span>
                    <span v-else-if="request.status == 1"class="badge badge-info">Confirmed</span>
                    <span v-else-if="request.status == 2"class="badge badge-purple">Prechecked</span>
                    <span v-else-if="request.status == 3"class="badge badge-warning">Bed Assigned</span>
                    <span v-else-if="request.status == 4"class="badge badge-warning">Billed</span>
                    <span v-else-if="request.status == 10"class="badge badge-success">Admitted</span>
                    <span v-else-if="request.status == 10"class="badge">Discharged</span>
                    <span v-else class="badge badge-danger">Delete</span>
                </td>
                <td>
                    <button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="dropdown-item btn btn-block btn-sm" @click="viewRequest(request)"><i class="fa fa-eye mr-1 text-primary"></i> View Request</button>
                        <button v-if="request.status == 0" class="dropdown-item btn btn-block btn-sm" @click="confirmRequest(request)"><i class="fa fa-check mr-1 text-purple"></i> Confirm Request</button>
                        <button v-if="request.status == 1" class="dropdown-item btn btn-block btn-sm" @click="precheckRequest(request)"><i class="fa fa-list mr-1 text-dark"></i> Precheck Request</button>
                        <button v-if="request.status == 2" class="dropdown-item btn btn-block btn-sm" @click="bedAssignment(request)"><i class="fa fa-bed mr-1 text-success"></i> Assign Bed</button>
                        <button v-if="request.status >= 4 && request.status < 10" class="dropdown-item btn btn-block btn-sm" @click="admitRequest(request)"><i class="fa fa-list mr-1 text-dark"></i> Admit Request</button>
                        <button v-if="request.status == 0"class="dropdown-item btn btn-block btn-sm" @click="updateRequest(request)"><i class="fa fa-edit mr-1 text-warning"></i> Update Request</button>
                        <button v-if="request.status == 0" class="dropdown-item btn btn-block btn-sm" @click="deactivateRequest(request)"><i class="fa fa-times mr-1 text-danger"></i> Cancel Request</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="9" class="text-center">No Room Types Found</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            loading: false,
            request: {},
        }
    },
    emits:['refreshRequestList'],
    methods: {
        addRequest(){
            this.loading = true;
            this.editMode = false;
            this.request = {};
            $('#requestFormModal').modal('show');
            this.loading = false;
        },
        admitRequest(request){
            this.loading = true;
            this.editMode = false;
            this.request = request;
            $('#requestFormModal').modal('show');
            this.loading = false;
        },
        bedAssignment(request){
            this.loading = true;
            this.request = request;
            $('#bedAssignmentFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            this.$emit('refreshRequestList');
            $('#bedAssignmentFormModal').modal('hide');
            $('#requestFormModal').modal('hide');
            $('#requestViewModal').modal('hide');            
        },
        confirmRequest(request){
            this.$swal.fire({
                title: 'Are you sure?',
                text: 'You are about to confirm this admission! A bill will be generated',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Confirm it!',
                cancelButtonText: 'Cancel'
            })
            .then((result) => {
                if (result.isConfirmed) {
                    this.loading = true;
                    axios.get('/api/emr/admissions/requests/'+request.id+'/confirm')
                    .then((response)=>{
                        this.$swal.fire('Confirmed!', 'Request has been confirmed.', 'success');
                        this.$emit('refreshRequestList');
                        this.loading = false;
                    })
                    .catch((error)=>{
                        this.loading = false;
                        this.$swal.fire('Error!', 'An error occurred while confirming request.', 'error');
                    });
                }
            });
        },
        deactivateRequest(request){
            this.$swal.fire({
                title: 'Are you sure?',
                text: 'You are about to deactivate this room type!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Deactivate it!',
                cancelButtonText: 'Cancel'
            })
            .then((result) => {
                if (result.isConfirmed) {
                    this.loading = true;
                    axios.delete('/api/emr/admissions/requests/'+request.id)
                    .then((response)=>{
                        this.$swal.fire(
                            'Deactivated!',
                            'Room Type has been deactivated.',
                            'success'
                        );
                        this.$emit('refreshRequestList');
                        this.loading = false;
                    })
                    .catch((error)=>{
                        this.loading = false;
                        this.$swal.fire(
                            'Error!',
                            'An error occurred while deactivating room type.',
                            'error'
                        );
                    });
                }
            });
        },
        precheckRequest(request){
            this.loading = true;
            this.editMode = true;
            this.request = request;
            $('#precheckFormModal').modal('show');
            this.loading = false;
        },
        updateRequest(request){
            this.loading = true;
            this.editMode = true;
            this.request = request;
            $('#requestFormModal').modal('show');
            this.loading = false;
        },
        viewRequest(request){
            this.loading = true;
            this.request = request;
            $('#requestViewModal').modal('show');
            this.loading = false;
        },
    },
    mounted() {
        
    },
    props:{
        requests: Array,
    }
}
</script>