<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-12" >
                <div class="card">
                    <div class="card-header">
                        Referred In Requests
                    </div>
                    <LaboratoryDetailQueue :requests="requests" source="laboratory" actionable="yes" />
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            requests: [],
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addApplicant(){
            this.$Progress.start();
            this.editMode = false;
            Fire.$emit('ApplicantDataFill', {});
            $('#applicantModal').modal('show');
            this.$Progress.finish();
        },
        addAppointment(){
            this.$Progress.start();
            this.editMode = false;
            this.appointment = {};
            Fire.$emit('AppointmentDataFill', {});
            $('#appointmentModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(page=1) {
            this.$Progress.start();
            axios.get('/api/emr/laboratory/requests/reffered_in')
            .then(response => {
                this.refreshDashboard(response);
                this.$Progress.finish();
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },

        refreshDashboard(response) {
            this.requests = response.data.requests;
        }
    },
    props: {}
}
</script>