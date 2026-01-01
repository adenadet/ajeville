<template>
<section clas="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th v-if="source == 'all'">Designation</th>
                <th>Title</th>
                <th v-if="source == 'all'">Max Score</th>
                <th>Description</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="kpis.length > 0">
            <tr v-for="kpi in kpis" :key="kpi.id">
                <td v-if="source == 'all'">{{ kpi.designation != null ? kpi.designation.name : 'Not Yet Assigned'}}</td>
                <td>{{  kpi.title }}</td>
                <td v-if="source == 'all'">{{ kpi.max_score }}</td>
                <td :title="kpi.description" v-html="readMore(kpi.description, 25, '...') "></td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td :colspan="source == 'all' ? 4 : 2">No Kpis yet</td>
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
            kpi: {},
        }
    },
    emits: ['refreshDesignation'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        createDesignationKpi() {
            this.loading = true;
            this.designationData.post('/api/hrms/designation_kpis')
                .then(response => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Designation created successfully!',
                    });
                    this.$emit('refreshDesignation', response);
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
            axios.get('/api/hrms/designations/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Designation Form not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.departments = response.data.departments;
        },
        updateDesignationKpi() {
            this.loading = true;
            console.log(this.designationData.id);
            this.designationData.put(`/api/hrms/designation_kpis/${this.designationData.id}`)
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Designation updated successfully!',
                });
                this.$emit('refreshDesignation', response);
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
        kpis: Array,
        source: String,
    },
}
</script>