<template>
<section class="content-header p-0">
    <div class="container-fluid">
        <EServiceFormSearch search_type="certificates" @searchedAppointments="refreshAppointments" />
        <div class="row">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header"><h3 class="card-title">Certificates</h3></div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Applicant</th>
                                    <th>Date</th>
                                    <th>Front Officer</th>
                                    <th>Consultant</th>
                                    <th>Radiologist</th>
                                    <th>Issued By</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody v-if="appointments.data == null || appointments == null">
                                <tr>
                                    <td colspan="6" class="text-center">You have not made any appointments yet</td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr v-for="(appointment, index) in appointments.data" :key="appointment.id">
                                    <td>{{addOne(index)}}</td>
                                    <td>{{FullName(appointment.patient)}} <br /><small>{{appointment.service_id != null && appointment.service != null ? appointment.service.name : ''}}</small></td>
                                    <td>{{ExcelDate(appointment.date)}} <br /><small>{{appointment.schedule}}</small></td>
                                    <td>{{FullName(appointment.front_officer)}}</td>
                                    <td>{{FullName(appointment.medical_officer)}}</td>
                                    <td>{{FullName(appointment.radiologist)}}</td>
                                    <td>{{FullName(appointment.issuing_officer)}}</td>
                                    <td><span class="tag tag-success">{{appointment.status == 0 ? 'Unpaid' :(appointment.status == 1 ? 'Paid' :(appointment.status == 2 ? 'Reschedule' :(appointment.status == 3 ? 'Cancelled' : (appointment.status == 8 ? 'Completed' : (appointment.status == 10 ? 'Certificate Sent' :'Done')))))}}</span></td>
                                    <td>
                                        <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                            <router-link :to="'./appointment/'+appointment.id" class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary mr-1"></i> View Appointment</router-link>
                                            <a v-show="appointment.status == 10" :href="'/certificates/'+appointment.id+'?p='+appointment.patient.passport_no+'&f='+appointment.patient.first_name"  class="dropdown-item btn btn-block btn-sm" target="_blank"><i class="fa fa-certificate text-success mr-1"></i> Print Certificate</a>
                                            <button v-show="appointment.status == 10" class="dropdown-item btn btn-block btn-sm" @click="viewReport(appointment)"><i class="fa fa-file mr-1"></i> View Report</button>
                                            <a v-show="appointment.referral !=null" :href="'/eservices/front_office/referral/'+appointment.id"  class="dropdown-item btn btn-block btn-sm" target="_blank"><i class="fa fa-file-pdf text-warning"></i> Print Referral</a>
                                        </div> 
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="appointments.per_page != null ? appointments.per_page : 52" :records="appointments.total != null ? appointments.total : 550" >
                        </pagination>
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
            appointments:{},
            appointment: {},
            current_page: 1,
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials(page=1) {
            this.loading = true;
            axios.get('/api/emr/appointments/certificates?page='+page)
            .then(response => {this.refreshAppointments(response); this.loading = false;})
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'List of Certificates did not loaded successfully',})
            });
        },
        refreshAppointments(response) {
            this.appointments = response.data.appointments;
        },
        viewReport(appointment){
            this.appointment = appointment;
            $('#reportModal').modal('show');
        },
    },
    props: {}
}
</script>