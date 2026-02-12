<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"></div>
                <div class="card-body p-0">
                    <EMRAdmissionDetailRequest :request.sync="request" />
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
            this.closeModals();
            axios.get('/api/emr/admissions/room_types?type='+this.type+'&query='+this.query+'&page='+this.current_page)
            .then(res => {
                this.room_types = res.data.room_types;
            })
            .finally(() => {
                this.loading = false
            })
        },
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