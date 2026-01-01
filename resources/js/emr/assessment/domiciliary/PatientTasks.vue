<template>
<div class="card card-primary">
    <div class="modal fade" id="patientTaskModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-show="editMode">Edit Task</h4>
                    <h4 class="modal-title" v-show="!editMode">New Task</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <NurseFormPatientTask :patient="patient" :editMode="editMode" :domiciliary="domiciliary" />
                </div>
            </div>
        </div>
    </div>
    <div class="card-header">
        <h6 class="card-title">List of Tasks</h6>
        <div class="card-tools"><button type="submit" class="btn btn-sm btn-tool" @click="addTask"><i class="fas fa-plus"></i></button></div>
    </div>
    <div class="card-body table-responsive p-0" style="height: 300px;">
        <table  v-if="tasks != null && tasks.length != 0" class="table table-head-fixed table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Frequency</th>
                    <th>Amount</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="patient_task in tasks" :key="patient_task.id">
                    <td>{{patient_task.task.name}}</td>
                    <td>{{patient_task.start_date}}</td>
                    <td>{{patient_task.end_date}}</td>
                    <td><span class="tag tag-danger">{{patient_task.frequency != null ? patient_task.frequency.name: 'Not Applicable'}}</span></td>
                    <td>{{patient_task.quantity}}</td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-primary" @click="editTask(patient_task)"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger" @click="deleteTask(patient_task.id)"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <table v-else class="table table-head-fixed table-hover text-nowrap">
            <tbody>
                <tr><td><p>No Tasks has been created</p></td></tr>
            </tbody>
        </table>
    </div>
</div>
</template>
<script>
import NurseFormPatientTask from '../nursing/forms/PatientTask.vue';
export default {
    components:{
        NurseFormPatientTask
    },
    data() {
        return {
            editMode: true,
            domiciliary: 1,
        }
    },
    mounted() {  
        Fire.$on('refreshPatientTasks', () => {
            this.closeModal();
            Fire.$emit('refreshDomiciliaryPatient');
        });  
    },
    methods: {
        addTask(){
            this.$Progress.start();
            this.editMode = false;
            this.patient_task = {};
            Fire.$emit('patientTaskDataFill', {});
            $('#patientTaskModal').modal('show');
            this.$Progress.finish();
        },
        closeModal(){
            $('#patientTaskModal').modal('hide');
        },
        deleteTask(id){
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
                if(result.value){
                    this.form.delete('/api/emr/nursing/patient_tasks/'+id)
                    .then(response=>{
                        Swal.fire('Confirmed!', 'The Patient Task has been deleted.', 'success');
                        this.refreshDomiciliaries(response);   
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editTask(task){
            this.$Progress.start();
            this.editMode = true;
            this.patient_task = task;
            Fire.$emit('patientTaskDataFill', task);
            $('#patientTaskModal').modal('show');
            this.$Progress.finish();
        },
    },
    props: {
        tasks: Array,
        patient: Object,
    }
}
</script>