<template>
<section class="col-md-12">
    <div class="modal fade" id="periodModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Period Detail</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsDetailAssessmentPeriod :period.sync="period" @reload="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="periodFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{editMode ? 'Update ': 'Create New '}} Period</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormAssessmentPeriod :period.sync="period" :editMode.sync="editMode" @refreshPeriod="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-navy">
            <h3 class="card-title">Assessment Periods</h3>
            <div class="card-tools">
                <div class="input-group input-group" style="width: 650px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                    <div class="input-group-append">
                        <input type="date" name="start_date" id="start_date" class="form-control float-right ml-1" placeholder="Start Date" v-model="start_date">
                        <input type="date" name="end_date" id="end_date" class="form-control float-right ml-1" placeholder="End Date" v-model="end_date">
                        <button type="button" class="btn btn-default" @click="getAllInitials"><i class="fas fa-search"></i></button>
                        <select name="table_search" class="form-control float-right ml-1" v-model="type" @change="getAllInitials">
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <button type="button" class="btn btn-primary" @click="addPeriod"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0 overlay-wrapper" style="height: 600px;">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <table class="table table-head-fixed text-nowrap table-stripped ">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody v-if="periods.total > 0">
                    <tr v-for="period in periods.data">
                        <td>{{ period.name }}</td>
                        <td>{{ ExcelDate(period.start_date) }}</td>
                        <td>{{ ExcelDate(period.end_date) }}</td>
                        <td>
                            <span v-if="period.status == 1" class="badge badge-success">Active</span>
                            <span v-else-if="period.status == 0" class="badge badge-danger">Inactive</span>
                        </td>
                        <td v-html="readMore(period.notes, 25, '...')" :title="period.notes"></td>
                        <td>
                            <button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <router-link :to="'/hrms_admin/assessment_periods/'+period.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Assessment Period</button></router-link>
                                <button class="dropdown-item btn btn-block btn-sm"  @click="editPeriod(period)"><i class="fa fa-edit mr-1 text-warning"></i> Edit Assessment Period</button>
                                <button v-if="period.status == 1" class="dropdown-item btn btn-block btn-sm" @click="deactivatePeriod(period.id)"><i class="fa fa-recycle mr-1 text-danger"></i> Deactivate Period</button>
                                <button v-if="period.status == 0" class="dropdown-item btn btn-block btn-sm" @click="deactivatePeriod(period.id)"><i class="fa fa-recycle mr-1 text-success"></i> Reactivate Period</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td>No Period Yet</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="periods.per_page != null ? periods.per_page : 52" :records="periods.total != null ? periods.total : 550" ></pagination>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            current_page: 1,
            editMode: false,
            end_date: '',
            form: new Form({}),
            loading: false,
            period: {},
            periods: {data: [], total: 0},
            query: '',
            start_date: '',
            type: '',
        }
    },
    methods:{
        addPeriod(){
            this.editMode = false;
            this.period = {};
            $('#periodFormModal').modal('show');
        },
        closeModals(){
            $('#periodModal').modal('hide');
            $('#periodFormModal').modal('hide');
        },
        deletePeriod(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/hrms/assessment_periods/'+id)
                    .then(response=>{
                        this. $swal.fire('Deleted!', response.data.message, 'success');
                        this.getAllInitials();
                        this.loading = false;   
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        editPeriod(period){
            this.loading = true;
            this.editMode = true;
            this.period = period;
            $('#periodFormModal').modal('show');
            this.loading = false;
        },
        getAllInitials(){
            this.loading = true
            axios.get('/api/hrms/assessment_periods?end_date='+this.end_date+'&query='+this.query+'&start_date='+this.start_date+'&type='+this.type+'&page='+this.current_page).then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({icon: 'success', title: 'Periods loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Periods not loaded successfully',})
            });
        },
        refreshPage(response){
            this.periods = response.data.periods;
            this.closeModals();
        },
        searchDesignation(){
            axios.get('/api/hrms/periods/search/'+this.query)
            .then((response ) => {this.periods = response.data.periods;})
            .catch(()=>{});
        },
        viewPeriod(period){
            this.period = period;
            $('#periodModal').modal('show');
        },
    },
    mounted(){ 
        this.getAllInitials();
    },
}
</script>