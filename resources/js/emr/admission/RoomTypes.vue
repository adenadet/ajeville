<template>
<section>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Room Types</h3>
                <div class="card-tools">
                    <div class="input-group" style="width: 350px;">
                        <input type="text" name="table_search" v-model="query" class="form-control mr-1" placeholder="Search">
                        <div class="input-group-append">
                            <select class="form-control ml-1" v-model="type">
                                <option value="all">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <button type="button" class="btn btn-default ml-1" @click="getAllInitials"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 600px">
                <EMRAdmissionDetailRoomTypeList :room_types="room_types.data" @refreshRoomTypeList="getAllInitials" />
            </div>
            <div class="card-footer">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="room_types.per_page != null ? room_types.per_page : 52" :records="room_types.total != null ? room_types.total : 550" ></pagination>
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
            room_types: {data: [], total: 0},
            service_types: [],
            specialties: [],
            statuses: ['pending', 'confirmed', 'checked_in', 'completed', 'cancelled', 'no_show'],
            type: 'active',
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
            axios.get('/api/emr/admissions/room_types?type='+this.type+'&query='+this.query+'&page='+this.current_page)
            .then(res => {
                this.room_types = res.data.room_types;
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