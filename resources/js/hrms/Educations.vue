<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="educationModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Update Education Details</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormEducation :editMode.sync="editMode" :education.sync="education" @refreshEducation="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Educations</h3>
            <div class="card-tools">
                <button class="btn btn-primary btn-sm" @click="addEducation"><i class="fa fa-plus"></i> Add Education</button>
            </div>
        </div>
        <div class="card-body table-responsive p-0" style="height: 500px;">
            <HrmsDetailEducationList :educations="educations.data" @refreshEducationPage="getAllInitials" />
        </div>
        <div class="card-footer">
            
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
            educations: {data: []},
            education: {},
            loading: false,
        }
    },
    methods:{
        addEducation(){
            this.training = {},
            this.editMode = false;
            $('#educationModal').modal('show');
        },
        closeModals(){
            $('#assignEducationModal').modal('hide');
            $('#educationModal').modal('hide');
        },
        deleteEducation(id){
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
        editEducation(training){
            this.editMode = true;
            this.training = training;
            //Fire.$emit('educationDataFill', training);
            $('#educationModal').modal('show');
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