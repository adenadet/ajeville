<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-3">
            <div class="card">

            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payslips</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <HrmsDetailPayslipList />
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
            leave_request: {},
            loading: false,
        }
    },
    mounted() {},
    methods: {
        getInitials() {
            this.loading = true;
            axios.get('/api/hrms/leaves/'+leave_request_id+'?type='+this.source)
            .then(response => {
                this.leave_request = response.data.leave_request;
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
    },
    props: {
        payslips: Array,
        source: String,
    },
    watch:{}
}
</script>