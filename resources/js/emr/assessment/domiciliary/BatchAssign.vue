<template>
<section>
    <DomFormDailySearch search_type="batch_assign"/>
    <div class="modal fade" id="assignModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Assign Batch</h4>
                    <button type="button" class="close" data-dismiss="modal" @click="closeBatch()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <DomFormAssignBatch :editMode="editMode" :staffs="staffs"/>
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
                    <!--DomFormBatch :editMode="editMode" :patients="patients" :staff_types="staff_types" :shift_types="shift_types"/-->
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 class="card-title"><i class="fa fa-calendar mr-1"></i>Batch Assign</h3></div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Shift Type | Staff Type</th>
                        <th>Patient </th>
                        <th>Assign Staff </th>
                        <th>Arrival</th>
                        <th>Departure</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(batch, index) in daily_batches.data" :key="batch.id">
                    
                        <td>{{batch.raw_date}}</td>
                        <td>{{(batch.shift_type != null ? batch.shift_type.name : 'Old Shift Type')}} | {{(batch.staff_type != null ? batch.staff_type.name : 'Old staff Type')}}</td>
                        <td>{{(batch.patient != null ? batch.patient.first_name+' '+batch.patient.middle_name+' '+batch.patient.last_name : 'Old Patient')}}</td>
                        <td>{{batch.user_id != null ? batch.first_name+' '+batch.middle_name+' '+batch.last_name: 'Unassigned'}}</td>
                        <td>{{batch.start_date}}</td>
                        <td>{{batch.end_date}}</td>
                        <td v-html="batch.status == null ? 'Unassigned' : batch.status == 0 ? 'Not Arrived': (batch.status == 1 ? 'Arrived':(batch.status == 2 ? 'Departed': 'Confirmed'))"></td>
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
            <pagination :data="daily_batches" @pagination-change-page="getBatches">
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
            daily_batches: {},
            form: new Form({}),
            staffs: [],
            today:"",
        }
    },
    mounted() {
        const date =new Date().toJSON().slice(0, 10);
        this.today = date;
        Fire.$on('refreshShiftTypes', response=>{
            this.refresh(response);
            this.closeShiftType();
        });
        Fire.$on('refreshDailySearch', response=>{
            this.refresh(response);
        });
        Fire.$on('shiftResponse', response=>{
            this.getInitials();
            this.closeBatch();
             
        });
        this.getInitials();
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
        assignBatch(batch){
            this.$Progress.start();
            this.editMode = false;
            Fire.$emit('assignBatchDataFill', batch);
            $('#assignModal').modal('show');
            this.$Progress.finish();
        },
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
            axios.get('/api/emr/domiciliary/batch_assigns').then(response =>{
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
            this.daily_batches = response.data.daily_batches;
            this.patients = response.data.patients;
            this.staff_types = response.data.staff_types;
            this.shift_types = response.data.shift_types;
            this.staffs = response.data.staffs;
        }
        
    },
    props:{
    }
}
</script>