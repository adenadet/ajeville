<template>
    <section class="container-fluid">
        <EServiceDetailReferral :appointment.sync="appointment" :referral.sync="appointment.referral" />
    </section>
</template>
<script>
export default {
    data() {
        return {
            appointment: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials() {
            axios.get('/api/emr/appointments/referral/'+this.$route.params.id)
            .then(response=>{
                this.appointment = response.data.appointment;   
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'List of Certificates did not loaded successfully',})
            });
        },
    },
    props: {}
}
</script>