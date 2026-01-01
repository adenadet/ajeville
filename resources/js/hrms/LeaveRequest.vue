<template>
    <section>
    
    </section>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            editMode: true,
            loading: false,
            request: {},
            requests: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/hrms/leaves/requests/?type=mine&page='+page)
            .then(response => {
                this.loading = false;
                this.refreshConsultations(response)
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Your consultations did not loaded successfully',})
            });
        },
        refreshConsultations(response) {
            this.consultations = response.data.appointments;
            this.nations = response.data.nations;
        }
    },
    props: {}
}
</script>