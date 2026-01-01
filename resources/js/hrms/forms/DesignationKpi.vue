<template>
    Put a single form here for a KPI
</template>
<script>
export default {
    data() {
        return {
            designations: [],
            kpiData : new Form({
                id: '',
                description: '',
                designation_id: '',
                max_score: '',
                status: '',
                title: '',
            }),
            loading: false,
        }
    },
    emits: ['refreshKpiForm'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        createDesignationKpi() {
            this.loading = true;
            this.kpiData.post('/api/hrms/designation_kpis')
                .then(response => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Designation KPI created successfully!',
                    });
                    this.$emit('refreshKpiForm', response);
                    this.loading = false;
                })
                .catch(error => {
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong while creating education.',
                    });
                    this.loading = false;
                });
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/hrms/designation_kpis/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Designation KPI Form not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.designations = response.data.designations;
        },
        updateDesignationKpi() {
            this.loading = true;
            this.kpiData.put(`/api/hrms/designation_kpis/${this.kpiData.id}`)
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Designation KPI updated successfully!',
                });
                this.$emit('refreshKpiForm', response);
                this.loading = false;
            })
            .catch(error => {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong while updating designation.',
                });
                this.loading = false;
            });
        },

    },
    props: {
        designation: Object,
        kpi: Object,
    },
    watch: {
        kpi(){
            this.loading = true;
            this.kpiData.fill(this.kpi);
            this.loading = false;
        }
    }
}
</script>