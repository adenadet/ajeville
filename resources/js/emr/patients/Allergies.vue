<template>
<div class="card card-primary">
    <div class="modal fade" id="allergyModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title" v-html="editMode ? 'Edit Allergy' : 'Add Allergy'"></h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button></div>
                <div class="modal-body"><HimsFormAllergy :editMode="editMode" :allergy="allergy" /></div>
            </div>
        </div>
    </div>
    <div class="card-header">
        <h3 class="card-title">List of Allergies</h3>
        <div class="card-tools"><button type="submit" class="btn btn-sm btn-default" @click="addAllergy()"><i class="fas fa-plus"></i></button></div>
    </div>
    <div class="card-body">
        <div class="row" v-if="(allergies != null) && (allergies.data != null) && (allergies.data.length != 0)">
            <div class="col-sm-4" v-for="allergy in allergies.data" :key="allergy.id">
                <div class="card">
                    <div class="ribbon-wrapper"><div class="ribbon bg-maroon disabled">Allergy</div></div>
                    <p v-html="allergy.allergy"></p>
                    <small v-html="allergy.description"></small>
                    <div class="card-footer clearfix">
                        <button type="button" class="btn btn-sm btn-danger float-right" title="Delete Allergy" @click="deleteAllergy(allergy.id)"><i class="fas fa-trash"></i></button>
                        <button type="button" class="btn btn-sm btn-primary float-right" title="Edit Allergy" @click="editAllergy(allergy)"><i class="fas fa-edit"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m-0 p-3" v-else>        
            <div class="col-sm-12">No Allergy has been reported for this patient</div>
        </div>
    </div>
    <div class="card-footer">
        <pagination :data="allergies" @pagination-change-page="getInitials">
            <span slot="prev-nav">&lt; Previous </span>
            <span slot="next-nav">Next &gt;</span>
        </pagination>
    </div>
</div>
</template>
<script>
//import NurseFormPatientTask from '../nursing/forms/PatientTask.vue';
export default {
    //components:{NurseFormPatientTask},
    data() {
        return {
            allergy: {},
            allergies: {},
            domiciliary: 1,
            editMode: true,
            form: new Form({}),
            user: {},
        }
    },
    mounted() {  
        Fire.$on('refreshPatientAllergies', patient => {
            this.patient = patient;
            this.getInitials();
            this.closeModal();
        });  
    },
    methods: {
        addAllergy(){
            this.$Progress.start();
            this.editMode = false;
            let details = {'allergy': {}, 'patient':this.patient};
            Fire.$emit('AllergyDataFill', (details));
            $('#allergyModal').modal('show');
            this.$Progress.finish();
        },
        closeModal(){
            $('#allergyModalModal').modal('hide');
        },
        deleteAllergy(id){
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
                    this.form.delete('/api/emr/hims/allergies/'+id)
                    .then(response=>{
                        this.allergies = response.data.allergies;
                        Swal.fire('Confirmed!', 'The Allergy has been deleted.', 'success');    
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editAllergy(allergy){
            this.$Progress.start();
            this.editMode = true;
            this.patient_task = task;
            Fire.$emit('patientTaskDataFill', task);
            $('#patientTaskModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(page=1){
            if (this.patient != null){
                axios.get('/api/emr/hims/allergies/'+this.patient.id+'?page='+page).then(response =>{
                    this.$Progress.finish();
                    this.allergies = response.data.allergies;
                })
                .catch(()=>{
                    this.$Progress.fail();
                    toast.fire({icon: 'error', title: 'Allergies failed to load successfully',});
                });
            }
            else{this.allergies = [];}
        },
    },
    props: {}
}
</script>