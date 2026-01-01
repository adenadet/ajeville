<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>S/N</th>
                <th v-if="source == 'admin'">{{ type == 'Applicant' ? 'Applicant' : 'Employee'}}</th>
                <th>Training</th>
                <th>Institution</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="trainings.length > 0">
            <tr v-for="(training, index) in trainings" :key="training.id">
                <td>{{ addOne(index) }}</td>
                <td v-if="source == 'admin'">{{ training.user != null ? FullName(training.user) : 'Invalid User' }}</td>
                <td>{{ training.name }}</td>
                <td>{{ training.institution }}</td>
                <td>{{ training.date }}</td>
                <td><button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="dropdown-item btn btn-block btn-sm" @click="viewTraining(training)"><i class="fa fa-eye mr-1 text-primary"></i> View Training</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateTraining(training)"><i class="fa fa-edit mr-1 text-warning"></i> Update Training</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="deleteTraining(training)"><i class="fa fa-user mr-1 text-danger"></i> Delete Training</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan="6">No Training was found</td></tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            training: {},
        }
    },
    emits: ['refreshTrainingsPage'],
    methods:{
        closeModals(){
            $('#trainingModal').modal('hide');
            $('#trainingFormModal').modal('hide');
        },
        deleteTraining(id){
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
                    this.form.delete('/api/hrms/trainings/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        updateTraining(training){
            this.training = training;
            this.editMode = true;
            $('#trainingFormModal').modal('show');
        },
        refreshPage(response){
            this.$emit('refreshTrainingsPage');
            this.closeModals();
        },
        viewTraining(training){
            this.training = training;
            $('#trainingModal').modal('show');
        }
    },
    mounted(){ 
        //this.getAllInitials();
    },
    props:{
        trainings: Array,
        source: String,
        style: String,
    }
}
</script>