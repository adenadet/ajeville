<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="appointmentConfirmModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Start Visit</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRFrontOfficeFormCheckIn :appointment.sync="appointment" :editMode="editMode" @refreshAppointment="refreshPage" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="appointmentModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{editMode ? 'Update Appointment Booking' : 'Add New Appointment Booking'}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRFrontOfficeFormAppointment :appointment.sync="appointment" :editMode="editMode"  @refreshAppointmentForm="refreshPage" />
                </div>
            </div>
        </div>
    </div>
    <table class="table table-hover table-striped text-nowrap mb-0">
        <thead class="table-light">
            <tr>
                <th>Patient</th>
                <th>Date</th>
                <th>Time</th>
                <th>Service Type</th>
                <th>Specialty</th>
                <th>Consultant</th>
                <th>Status</th>
                <th class="text-end"><button class="btn btn-primary btn-sm float-right" @click="addAppointment"><i class="fa fa-plus mr-1"></i>Add </button></th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="loading">
                <td colspan="8" class="text-center py-4">Loading appointments...</td>
            </tr>
            <tr v-for="a in appointments" :key="a.id">
                <td v-if="a.patient != null">
                    <strong>{{ FullName(a.patient.user) }}</strong><br />
                    <small class="text-muted">{{ a.patient.unique_id }}</small>
                </td>
                <td v-else>
                    Strange
                </td>
                <td>{{ ExcelDate(a.date) }}</td>
                <td>{{ a.time_slot }}</td>
                <td>{{ a.service_type.name }}</td>
                <td>{{ a.specialty?.name ?? '—' }}</td>
                <td>{{ a.consultant?.name ?? '—' }}</td>
                <td><span class="badge" :class="statusClass(a.status)">{{ a.status }}</span></td>
                <td class="text-end">
                    <span class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <router-link :to="'/appointments/'+a.unique_id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-1 text-primary"></i> View Appointment</router-link>
                        <button class="btn btn-block dropdown-item" v-if="canCheckIn(a)" @click="checkIn(a)"><i class="fas fa-calendar-check mr-1 text-success"></i> Check In Appointment</button>
                        <button class="btn btn-block dropdown-item" v-if="a.status == 'pending'"><i class="fas fa-calendar-alt mr-1 text-warning"></i> Reschedule</button>
                        <button class="btn btn-block dropdown-item" v-if="canCancel(a)" @click="cancelAppointment(a)"><i class="fas fa-trash mr-1 text-danger"></i> Delete</button>
                    </div>
                </td>
            </tr>
            <tr v-if="!loading && appointments != null && appointments.length === 0">
                <td colspan="8" class="text-center py-4">
                    No appointments found
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
            appointment: {},
            editMode: false,
            statuses: ['pending', 'confirmed', 'checked_in', 'completed', 'cancelled', 'no_show'],
        }
    },
    emits:['refreshAppointmentList'],
    methods: {
        addAppointment(){
            this.editMode = false;
            this.loading = true;
            this.appointment = {};
            $('#appointmentModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#appointmentModal').modal('hide');
        },
        statusClass(status) {
            return {
                'bg-secondary': status === 'pending',
                'bg-primary': status === 'confirmed',
                'bg-success': status === 'checked_in',
                'bg-dark': status === 'completed',
                'bg-danger': status === 'cancelled',
                'bg-warning': status === 'no_show'
            }
        },
        canCheckIn(a) {
            return ['pending', 'confirmed'].includes(a.status)
        },
        canCancel(a) {
            return !['cancelled', 'completed'].includes(a.status)
        },
        viewAppointment(a) {
            this.$router.push(`/appointments/${a.id}`)
        },
        checkIn(a) {
            this.loading = true;
            this.appointment = a;
            $('#appointmentConfirmModal').modal('show');
            this.loading = false;
        },
        cancelAppointment(a) {
        if (!confirm('Cancel this appointment?')) return

        axios.post(`/api/appointments/${a.id}/cancel`)
            .then(() => this.fetchAppointments())
        },
        refreshPage(){
            this.$emits('refreshAppointmentList');
        }

    },
    mounted() {},
    props:{
        appointments: Array,
    }
    
}
</script>

