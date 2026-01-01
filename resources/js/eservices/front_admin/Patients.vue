<template>
<section class="container-fluid">
    <div class="modal fade" id="applicantModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title" v-html="editMode ? 'Edit Patient' : 'Create Patient'"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EServiceFormPatient :editMode="editMode" :nations="nations" :applicant="applicant" /> 
                </div>
            </div>
        </div>
    </div>
        <div class="row">
            <div class="col-12 p-0">
                <div class="card p-0">
                    <div class="card-header bg-navy">
                        <h3 class="card-title">All Applicants</h3>
                        <div class="card-tools">
                            <button class="btn btn-xs btn-success" @click="addApplicant"><i class="fa fa-user-plus"></i> Create Applicant</button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0 overlay-wrapper">
                        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                        <table class="table table-hover table-striped text-nowrap">
                            <thead class="bg-dark">
                                <tr>
                                    <th>Reg. Date</th>
                                    <th>Applicant</th>
                                    <th>Date of Birth</th>
                                    <th>Sex</th>
                                    <th>Nationality</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody v-if="applicants.data == null || applicants == null">
                                <tr>
                                    <td colspan="6" class="text-center">You have not made any applicants yet</td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr v-for="(applicant, index) in applicants.data" :key="applicant.id">
                                    <td>{{ExcelDate(applicant.created_at)}} </td>
                                    <td>{{FullName(applicant)}}</td>
                                    <td>{{ExcelDate(applicant.dob)}}</td>
                                    <td>{{applicant.sex}}</td>
                                    <td>{{applicant.nationality ? applicant.nationality.name : 'Not Defined'}}</td>
                                    <td>
                                        <div class="btn btn-group">
                                            <button class="btn btn-primary btn-xs" @click="editApplicant(applicant)" title="Edit Applicant"><i class="fa fa-edit"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="applicants.per_page != null ? applicants.per_page : 52" :records="applicants.total != null ? applicants.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
</section>
</template>
<script>
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
const toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});
export default {
data() {
    return {
        applicant: {},
        applicants: {},
        appointments: {},
        current_page: 1,
        editMode: true,
        loading: false,
        nations: [],
        patients: [],
        services: [],
        user: {},
    }
},
mounted() {
    this.getAllInitials();
},
methods: {
    addApplicant(){
        this.editMode = false;
        this.applicant = {};
        $('#applicantModal').modal('show');
    },
    deleteApplicant(id){
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
            })
        .then((result) => {
            //Send Delete request
            if(result.value){
                this.form.delete('/api/emr/patients/'+id)
                .then(response=>{
                Swal.fire('Deleted!', 'Appointment has been deleted.', 'success');
                this.refreshAppointments(response);   
                })
                .catch(()=>{
                Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                });
            }
        });
    },
    editApplicant(applicant){
        this.editMode = true;
        this.applicant = applicant;
        $('#applicantModal').modal('show');
    },
    addAppointment(){
        this.$Progress.start();
        this.editMode = false;
        this.appointment = {};
        Fire.$emit('AppointmentDataFill', {});
        $('#appointmentModal').modal('show');
        this.$Progress.finish();
    },
    getAllInitials(page=1) {
        this.loading = true;
        axios.get('/api/emr/patients?page='+page)
        .then(response => {this.refreshAppointments(response);})
        .catch(() => {
            this.loading = false;
            this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
        });
    },
    refreshAppointments(response) {
        this.nations = response.data.nations;
        this.patients = response.data.patients;
        this.applicants = response.data.applicants;
        this.loading = false;
    }
},
props: {}
}
</script>