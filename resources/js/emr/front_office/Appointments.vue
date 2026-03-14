<template>
<section class="container-fluid">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="card-title">Appointments</h5>
            <div class="card-tools p-0 m-0">
                <div class="input-group input-group-sm" style="width: 550px;">
                    <div class="row p-0 mt-0">
                        <div class="col-md-3">
                            <input type="date" class="form-control" v-model="filters.date" />
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="filters.service_type_id">
                                <option value=''>All Service Types</option>
                                <option v-for="t in service_types" :key="t.id" :value="t.id">
                                    {{ t.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="filters.specialty_id">
                                <option value=''>All Specialties</option>
                                <option v-for="d in specialties" :key="d.id" :value="d.id">
                                    {{ d.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" v-model="filters.status">
                                <option value=''>All Status</option>
                                <option v-for="s in statuses" :key="s" :value="s">
                                    {{ firstUp(s) }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 p-1">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search patient..." v-model="filters.search"/>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-sm btn-default"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
            </div>
        </div>
        <div class="card-body border-bottom table-responsive p-0 overlay-wrapper" style="height: 600px;">
            <EMRFrontOfficeDetailAppointmentList :appointments="appointments.data" @refreshAppointmentList="getAllInitials()" />
        </div>
        <div class="card-footer">
            <div class="col-12">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="appointments.per_page != null ? appointments.per_page : 52" :records="appointments.total != null ? appointments.total : 550" ></pagination>
            </div>
        </div>
    </div>
  </section>
</template>
<script>
import EMRFrontOfficeDetailAppointmentList from '@/emr/front_office/details/AppointmentList.vue';
export default {
    components:{
        EMRFrontOfficeDetailAppointmentList
    },
    data() {
        return {
            loading: false,
            appointment: {},
            appointments: [],
            current_page: 1,
            departments: [],
            editMode: false,
            service_types: [],
            specialties: [],
            statuses: ['pending', 'confirmed', 'checked_in', 'completed', 'cancelled', 'no_show'],
            filters: {
                date: '',
                service_type_id: '',
                specialty_id: '',
                status: 'pending',
                search: ''
            }
        }
    },
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
        getAllInitials(){
            this.loading = true
            this.closeModals();
            axios.get('/api/emr/hims/appointments', {
                params: this.filters
            })
            .then(res => {
                this.appointments = res.data.appointments;
                this.service_types = res.data.service_types;
                this.specialties = res.data.specialties;
            })
            .finally(() => {
                this.loading = false
            })
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
        }
    },
    mounted() {
        this.getAllInitials()
        //this.fetchDepartments()
        //this.fetchAppointments()
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