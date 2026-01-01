<template>
<section>
    <div class="container-fluid">
        <div class="row" v-if="source == 'emr'">
            <div class="col-12 col-sm-4">
                <div class="info-box bg-light">
                    <div class="info-box-content">
                    <span class="info-box-text text-center text-muted">Chief Consultant</span>
                    <span class="info-box-number text-center text-muted mb-0">{{ branch.cinc != null ? FullName(branch.chief_consultant.user) : 'Not Yet Assigned' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="info-box bg-light">
                    <div class="info-box-content">
                    <span class="info-box-text text-center text-muted">Head of Nurses</span>
                    <span class="info-box-number text-center text-muted mb-0">{{ branch.hon != null ?  FullName(branch.hon.user): 'Not Yet Assigned'  }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="info-box bg-light">
                    <div class="info-box-content">
                    <span class="info-box-text text-center text-muted">Practice Manager</span>
                    <span class="info-box-number text-center text-muted mb-0">{{branch.pm != null ? FullName(branch.pm.user): 'Not Yet Assigned' }} </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
            
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            department: {},
            departments: {},
            form: new Form({}),
            loading: false,
        }
    },
    emits: ['refreshBranchList'],
    mounted() {
        this.getInitials();
    },
    methods: {
        deleteBranch(branch){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "Do you want to "+(branch.status == 1 ? "deactivate" : "reactivate")+" this branch?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Yes, "+(branch.status == 1 ? 'deactivate' : 'reactivate')+" it!",
            })
            .then((result) => {
                if (result.value) {
                    this.form.delete('/api/operations/branches/' + branch.id)
                    .then(response => {
                        this.$emit('refreshBranchList');
                        this.$swal.fire('Deleted!', 'Branch has been modified.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        getInitials() {
            this.loading = true;
            axios.get('/api/operations/departments/'+this.$route.params.id).then(response => {
                this.loading = false;
                this.refreshPage(response);
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'The departments did not load successfully',
                })
            });
        },
        refreshPage(response) {this.department = response.data.department;},
        updatePlan(){},
    },
    props: {
        branch: Object,
        source: String,
    }
}
</script>