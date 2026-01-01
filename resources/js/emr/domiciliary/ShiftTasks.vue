<template>
<section class="content-header">
    <div class="modal fade" id="completeTaskModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-html="editMode ? 'Edit Domiciliary Request' : 'Create Domiliciary Request'"></h4>
                    <button type="button" class="close" @click="closeRequest()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <NursingFormTaskComplete :task="task" :editMode="editMode"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Shift Details</h3>
            <div class="card-tools"><button class="btn btn-sm btn-primary" @click="confirmArrival($route.params.id)"><i class="fa fa-calendar-check"></i> Confirm Arrival</button></div>
        </div>
        <div class="row">
            <div class="col-4"><HimsPatientCard :patient="patient"/></div>
            <div class="col-8">
                <table class="table table-bordered table-hover" v-if="(shift.status == 0)">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="task in tasks" :key="task.id">
                            <td>{{task.task_details.name}}</td>
                            <td>{{task.task_details.description}}</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered table-hover" v-if="(shift.status == 1)">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="task in tasks" :key="task.id">
                            <td>{{task.task_details.name}}</td>
                            <td>{{task.task_details.description}}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-success" @click="completeActivity(task)"><i class="fa fa-check"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import HimsPatientCard from '../patients/Card.vue';
import NursingFormTaskComplete from '../nursing/forms/TaskComplete.vue';
export default {
    components:{HimsPatientCard, NursingFormTaskComplete},
    data(){
        return  {
            form: new Form({}),
            patient: {},
            task: {},
            tasks: [],
            shift: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        completeActivity(task){
            this.$Progress.start();
            this.task = task;
            Fire.$emit('completeDataFill', task);
            $('#completeTaskModal').modal('show');
            this.$Progress.finish();
        },
        confirmArrival(id){
            Swal.fire({
            title: 'Are you sure you are at the client\'s place?',
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
                this.form.put('/api/emr/domiciliary/batch_assigns/confirm/'+id)
                .then(response=>{
                    Swal.fire('Confirmed!', 'The Domiciliary Request has been confirmed.', 'success');
                    this.refresh(response);   
                })
                .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                });
            }
        });
        },
        getInitials(){
            axios.get('/api/emr/domiciliary/batch_assigns/'+this.$route.params.id).then(response =>{
                this.refresh(response);
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Profile loaded successfully',
                });
                Fire.$emit('BioDataFill', this.user);
                Fire.$emit('NextOfKinFill', this.nok);
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Profile not loaded successfully',
                })
            });
        },
        refresh(response){
            this.tasks = response.data.tasks;
            this.shift = response.data.shift;
            this.patient = response.data.patient;
        },
    },
    props:{
    }
}
</script>