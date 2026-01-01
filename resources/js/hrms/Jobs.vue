<template>
<section>
    <div class="row">
        <div class="modal fade" id="jobModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h4 class="modal-title">Job Form</h4>
                        <button type="button text-white" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true text-white"><i class="fa fa-times text-white"></i></span></button>
                    </div>
                    <div class="modal-body p-0">
                        <HrmsFormJob :editMode.sync="editMode" :job.sync="job" @refreshJobFormPage="getAllInitials"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Jobs List</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                <button type="button" class="btn btn-primary ml-1" @click="addJob()"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <HrmsDetailJobList :jobs.sync="jobs.data" />
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
            editMode: false,
            job: {},
            jobs: {data:[], total: 0},
            loading: false,
            query: '',
            status: '',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addJob(){
            this.loading = true;
            this.job = {};
            this.editMode = false;
            $('#jobModal').modal('show');
        },
        closeModals(){
            $('#jobModal').modal('hide');
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/hrms/jobs?type=status&query='+this.query+'&status='+this.status+'&page='+this.current_page)
            .then(response =>{
                this.jobs = response.data.jobs;
                this.closeModals();
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your leave allowances did not loaded successfully',
                })
            });
        },
        refreshPage(){},
    },
    props: {}
}
</script>