<template>
    <section >
        <div class="row">
            <div class="col-12">
                <div class="card overlay-wrapper">
                    <HrmsDetailLeaveAllowanceList :allowances="allowances.data" source="mine" />          
                    <div class="card-footer">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="allowances.per_page != null ? allowances.per_page : 52" :records="allowances.total != null ? allowances.total : 550" ></pagination>
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
            allowance: {},
            allowances: {data:[]},
            current_page: 1,
            editMode: false,
            loading: false,
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials(page=1, status=1){
            this.loading = true;
            axios.get('/api/hrms/leave_allowances?type=mine&status='+status+'&page='+page)
            .then(response =>{
                this.allowances = response.data.allowances;
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Your leave allowances did not loaded successfully',})
            });
        },
    },
    props: {}
}
</script>