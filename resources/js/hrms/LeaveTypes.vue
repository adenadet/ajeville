<template>
    <section class="container-fluid overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="modal fade" id="leaveTypeModal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-navy">
                            <h4 class="modal-title">{{ editMode ? 'Edit Leave Type: '+ leave_type.name : 'Create New Leave Type'}}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                        </div>
                        <div class="modal-body p-0">
                            <HrmsFormLeaveType :editMode="editMode" :leave_type="leave_type" @refreshPage="getAllInitials"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-navy">
                        <h3 class="card-title">All Leave Types</h3>
                        <div class="card-tools">
                            <button class="btn btn-sm btn-primary" @click="addNewLeaveType()" type="button"><i class="fa fa-plus mr-1"></i> Add New</button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped">
                        <thead class="bg-dark">
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Name</th>
                                <th>Number of Days</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody v-if="leave_types.data != null && leave_types != null">
                            <tr v-for="(leave_type, index) in leave_types.data" :key="leave_type.id">
                                <td>{{ addOne(index)  }}</td>
                                <td>{{ leave_type.name }}</td>
                                <td>{{ leave_type.no_of_days }}</td>
                                <td>{{ leave_type.start_date }}</td>
                                <td>{{ leave_type.end_date }}</td>
                                <td>
                                    <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                        <router-link :to="'/hrms_admin/leave_types/'+leave_type.id" class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Leave Type</router-link>
                                        <button class="dropdown-item btn btn-block btn-sm" @click="updateLeaveType(leave_type)"><i class="fa fa-edit mr-1 text-warning"></i> Update Leave Type</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan=6>No Leave Type has been created</td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="leave_types.per_page != null ? leave_types.per_page : 52" :records="leave_types.total != null ? leave_types.total : 550" ></pagination>
                    </div>
                </div>
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
            form: new Form({}),
            leave_types: {},
            leave_type: {},
            loading: false,
        }
    },
    methods:{
        addNewLeaveType(){
            this.leave_type = {},
            this.editMode = false;
            //Fire.$emit('leaveTypeDataFill', {});
            $('#leaveTypeModal').modal('show');
        },
        closeModals(){
            $('#assignLeaveTypeModal').modal('hide');
            $('#leaveTypeModal').modal('hide');
        },
        assignLeaveType(){},
        deleteLeaveType(id){
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
                if(result.value){
                    this.form.delete('/api/hrms/leave_types/'+id)
                    .then(response=>{
                        this.swal.fire('Deleted!', 'Leave Type has been deleted.', 'success');
                        this.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{
                        this.swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!',});}
                    );
                }
            });
        },
        editLeaveType(leave_type){
            this.editMode = true;
            this.leave_type = leave_type;
            //Fire.$emit('leaveTypeDataFill', leave_type);
            $('#leaveTypeModal').modal('show');
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/hrms/leave_types?page='+page+'&search='+this.search).then(response =>{
                this.reset(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Leave Types did not load successfully',});
            });
        },
        reset(response){
            this.leave_types = response.data.leave_types;
            this.closeModals();
        }
    },
    mounted() {
        this.getAllInitials();
        //Fire.$on('reloadLeaveTypes', response =>{this.reset(response);}); Fire.$on('reloadPage', () =>{this.getAllInitials();});
    }   
}
</script>