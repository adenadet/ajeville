<template>
<section>
    <div class="modal fade" id="assignModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Assign Batch</h4>
                    <button type="button" class="close" data-dismiss="modal" @click="closeBatch()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <DomFormBatch :editMode="editMode" :patients="patients" :staff_types="staff_types" :shift_types="shift_types"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="batchModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-show="editMode">Edit Batch</h4>
                    <h4 class="modal-title" v-show="!editMode">New Batch</h4>
                    <button type="button" class="close" data-dismiss="modal" @click="closeBatch()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <DomFormBatch :editMode="editMode" :patients="patients" :staff_types="staff_types" :shift_types="shift_types"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-calendar mr-1"></i>Nursing Tasks</h3>
            <div class="card-tools"><button type="button" class="btn btn-sm btn-primary" @click="addBatch">Add New</button></div>    
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(task, index) in tasks.data" :key="task.id">
                        <td>{{index | addOne}}</td>
                        <td>{{(task.shift_type != null ? task.shift_type.name : 'Old Shift Type')}}</td>
                        <td>{{(task.patient != null ? task.patient.first_name+' '+task.patient.middle_name+' '+task.patient.last_name : 'Old Patient')}}</td>
                        <td>{{(task.staff_type != null ? task.staff_type.name : 'Old staff Type')}}</td>
                        <td>{{task.start_date}}</td>
                        <td>{{task.end_date}}</td>
                        <td v-html="batch.status == 0 ? 'Pending': (batch.status == 1 ? 'Active': 'Inactive')"></td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-success" @click.prevent="assignBatch(batch)" title="Assign Batch to Staff"><i class="fa fa-user"></i></button>
                                <button class="btn btn-sm btn-primary" @click.prevent="editBatch(batch)" title="Edit Shift"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger" @click.prevent="deleteBatch(batch.id)" title="Assign Shift to Staff"><i class="fa fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <pagination :data="batches" @pagination-change-page="getTasks">
                <span slot="prev-nav">&lt; Previous </span>
                <span slot="next-nav">Next &gt;</span>
            </pagination>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            editMode: false,
            tasks: {},
            task:  {},
            form: new Form({}),
        
        }
    },
    mounted() {
        this.getInitials();
        Fire.$on('refreshShiftTypes', response=>{
            this.refresh(response);
            this.closeShiftType();
        });
    },
    methods:{
        addBatch(){
            this.$Progress.start();
            this.editMode = false;
            //this.shift_type = shift_type;
            Fire.$emit('batchDataFill', {});
            $('#batchModal').modal('show');
            this.$Progress.finish();
        },
        assignBatch(batch){},
        closeBatch(){
            $('#assignModal').modal('hide');
            $('#batchModal').modal('hide');
        },
        deleteBatch(id){
            Swal.fire({
                title: 'Are you sure, you want to delete this?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, confirm it!'
                })
            .then((result) => {
                //Send Confirm request
                if(result.value){
                    this.form.delete('/api/emr/domiciliary/batch_tasks/'+id)
                    .then(response=>{
                        Swal.fire('Confirmed!', 'The Batched Shift has been deleted.', 'success');
                        this.refreshAppointments(response);   
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editBatch(batch){
            this.$Progress.start();
            this.editMode = true;
            this.batch = batch;
            Fire.$emit('batchDataFill', batch);
            $('#batchModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(){
            this.$Progress.start();
            axios.get('/api/emr/domiciliary/batch_tasks').then(response =>{
                this.refresh(response);
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Courses were loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Courses were not loaded successfully',
                })
            });
        },
        getBatches(page=1){
            axios.get('/api/emr/domiciliary/batch_tasks?page='+page)
            .then(response=>{this.refresh(response);});
        },
        refresh(response){
            this.batches = response.data.batches;
            this.patients = response.data.patients;
            this.staff_types = response.data.staff_types;
            this.shift_types = response.data.shift_types;
        }
        
    },
    props:{
    }
}
</script>