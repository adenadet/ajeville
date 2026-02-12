<template>
<section class="row">
    <div class="col-md-3">
        <EMRAdmissionDetailRoom :room.sync="room" @refreshRoomDetail="getAllInitials"/>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Beds </h3>
                <div class="card-tools">
                    <!--div class="input-group" style="width: 250px;">
                        <input type="text" name="table_search" v-model="query" class="form-control" placeholder="Search">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary ml-1" @click="getAllInitials"><i class="fas fa-search"></i></button>
                        </div>
                    </div-->
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 600px">
                <div class="row">
                    <div class="col-md-3" v-for="bed in beds.data" :key="bed.id">
                        <EMRAdmissionDetailBed :bed="bed" @refreshBedList="getAllInitials"/>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="beds.per_page != null ? beds.per_page : 52" :records="beds.total != null ? beds.total : 550" ></pagination>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            bed: {},
            beds: {data: [], total: 0},
            current_page: 1,
            query: '',
            room: {},
        }
    },
    methods: {
        getAllInitials(){
            this.loading = true
            axios.get('/api/emr/admissions/rooms/'+this.$route.params.id+'?page='+this.current_page)
            .then(res => {
                this.beds = res.data.beds;
                this.room = res.data.room;
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