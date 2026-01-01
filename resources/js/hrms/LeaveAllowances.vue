<template>
    <section>
        <div class="row">
            <div class="modal fade" id="allowanceForm">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-navy">
                            <h4 class="modal-title">Allowance Form</h4>
                            <button type="button text-white" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true text-white"><i class="fa fa-times text-white"></i></span></button>
                        </div>
                        <div class="modal-body p-0">
                            <!--HrmsFormLeaveAllowance :editMode.sync="editMode" :leave_request.sync="leave_request" source="mine" @refreshPage="getAllInitials"/-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card overlay-wrapper">
                    <div class="card-header bg-navy">
                        <h3 class="card-title">{{source == 'mine' ? 'My' : 'All'}} Leave Allowances</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-xs btn-default"><i class="fas fa-search"></i></button>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-xs btn-primary" @click="addAllowance"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <HrmsDetailLeaveAllowanceList :allowances="allowances.data" source="admin" />          
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
        addAllowance(){
            this.allowance = {};
            this.editMode = false;
            $('#allowanceForm').modal('show');
        },
        closeModals(){
            $('#allowanceForm').modal('hide');
        },
        getAllInitials(page=1, status=1){
            this.loading = true;
            axios.get('/api/hrms/leave_allowances?type=status&status='+status+'&page='+page)
            .then(response =>{
                this.allowances = response.data.allowances;
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
    },
    props: {}
}
</script>