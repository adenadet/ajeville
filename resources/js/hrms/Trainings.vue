<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Trainings</h3>
            <div class="card-tools">
                <button class="btn btn-primary btn-sm" @click="addTraining"><i class="fa fa-plus"></i> Add Training</button>
            </div>
        </div>
        <div class="card-body table-responsive p-0" style="height: 500px;">
            <HrmsDetailTrainingList :trainings="trainings.data"  />
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
            trainings: {data: []},
            training: {},
            loading: false,
        }
    },
    methods:{
        addTraining(){
            this.training = {},
            this.editMode = false;
            $('#leaveTypeModal').modal('show');
        },
        closeModals(){
            $('#assignTrainingModal').modal('hide');
            $('#leaveTypeModal').modal('hide');
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
                if(result.value){
                    this.form.delete('/api/hrms/trainings/'+id)
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
        editTraining(training){
            this.editMode = true;
            this.training = training;
            //Fire.$emit('leaveTypeDataFill', training);
            $('#leaveTypeModal').modal('show');
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/hrms/trainings?page='+page+'&search='+this.search).then(response =>{
                this.reset(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Leave Types did not load successfully',});
            });
        },
        reset(response){
            this.trainings = response.data.trainings;
            this.closeModals();
        }
    },
    mounted() {
        this.getAllInitials();
    }   
}
</script>