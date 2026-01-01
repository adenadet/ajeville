<template>
    <section class="content-header p-0">
        <div class="row">
            <div class="col-12">
                <EServiceFormSearch search_type="front_admin" @searchedAppointments="refreshAppointments" />
                <div class="card overlay-wrapper">
                    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    <EServiceDetailAppointmentList source="front_office" :appointments.sync="appointments" />
                    <div class="card-footer">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="appointments.per_page != null ? appointments.per_page : 52" :records="appointments.total != null ? appointments.total : 550" ></pagination>
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
            appointments: {},
            current_page: 1,
            editMode: true,
            form: new Form({}),
            loading: false,

        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/emr/appointments?page='+page)
            .then(response=>{
                this.appointments = response.data.appointments;   
                this.loading = false;
            })
            .catch(() => {
                toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',});
                this.loading = true;
            });;
        },
        refreshAppointments(response) {
            this.appointments = response.data.appointments;
            this.services = response.data.services;
            this.nations = response.data.nations;
            this.patients = response.data.patients;
        },
    },
    props: {}
}
</script>