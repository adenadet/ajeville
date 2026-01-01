<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-hover text-nowrap">
        <thead class="bg-dark">
            <tr>
                <th>Patient</th>
                <th>Date of Birth</th>
                <th>City</th>
                <th>Phone Number</th>
                <th>Email Address</th>
                <th>Sex</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="patients.length == 0 || patients == null">
            <tr><td colspan="6" class="text-center">You have not made any patients yet</td></tr>
        </tbody>
        <tbody v-else>
            <tr v-for="patient in patients" :key="patient.id">
                <td>
                    <div class="user-block">
                        <img class="img-circle" src="">
                        <span class="username">{{patientName(patient)}}</span>
                        <span class="description">Registered {{ExcelDate(patient.created_at)}}</span>
                    </div>
                </td>
                <td>{{patient.user != null ? ExcelDate(patient.user.dob) : 'Undisclosed' }} </td>
                <td>{{patient.user != null ? patient.user.city : ''}} </td>
                <td>{{patient.user != null ? patient.user.phone : '' }} </td>
                <td>{{patient.user != null ? patient.user.email : ''}} </td>
                <td>{{patient.user != null ? patient.user.sex : ''}}</td>
                <td>
                    <span class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link :to="'./patients/'+patient.unique_id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-2 text-primary"></i> View Patient</router-link>
                        <button class="btn btn-block dropdown-item" @click="editPatient(patient)"><i class="fas fa-edit mr-2"></i> Update Patient Details</button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            loading: false,
            patient: {},
            status: '',
            user: {},
        }
    },
    mounted() {
        
    },
    methods: {
        closeModal(){
            $('#patientDetailModal').modal('hide');
            $('#patientFormModal').modal('hide');
        },
        editPatient(patient){
            this.loading = false;
            $('#patientFormModal').modal('show');
        },
        getApplicant(page=1){
            axios.get('/api/emr/hims/patients?page='+page)
            .then(response=>{
                this.refreshPatients(response); 
            });
        },
        getInitials() {
            this.loading = true;
            axios.get('/api/emr/hims/patients')
            .then(response => {this.loading = false; this.refreshPatients(response)})
            .catch(() => {
                this.loading = false;
                toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
        refreshPatients(response) {
            this.patients = response.data.patients;
        }
    },
    props: {
        patients: Array,
        source: String,
    }
}
</script>