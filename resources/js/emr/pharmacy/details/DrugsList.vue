<template>
<section class="container-fluid">
    <div class="modal fade" id="drugModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ editMode ? 'Edit Drug' : 'Create Drug'}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRPharmacyFormDrug :editMode="editMode" :drug.sync="drug" @refreshDrugForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>            
    <table class="table table-striped table-hover ">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Specific Drugs</th>
                <th>Interactions</th>
                <th>High Alert Medication</th>
                <th>Description</th>
                <th>Status</th>
                <th><button class="btn btn-primary btn-xs" @click="addDrug" type="button"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(drug, index) in drugs">
                <td>{{ addOne(index)}}</td>
                <td>{{ drug.name }} </td>
                <td>{{ drug.specific_drugs != null ? drug.specific_drugs.length : 'No Drug item linked' }}</td>
                <td>{{ drug.interactions != null ? drug.interactions.length : 'None' }}</td>
                <td>{{ drug.ham == 1 ? 'True' : 'False' }}</td>
                <td>{{ readMore(drug.description, 25, '...') }}</td>
                <td>
                    <span v-if="drug.status == 1" class="badge badge-success">Active</span>
                    <span v-else class="badge badge-danger">Inactive</span>
                </td>
                <td>
                    <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu">
                        <router-link class="btn btn-block dropdown-item" :to="'/operations/drugs/'+drug.id"><i class="fa fa-eye mr-1"></i> View </router-link>
                        <button class="btn btn-block dropdown-item" @click="updateDrug(drug)"><i class="fa fa-edit mr-1 text-primary"></i> Edit </button>
                        <button class="btn btn-block dropdown-item" @click="deleteDrug(drug.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete</button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
import EMRPharmacyFormDrug from '@/emr/pharmacy/forms/Drug.vue';
export default {
    components:{EMRPharmacyFormDrug},
    data(){
        return  {
            drug: {},
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits: ['refreshDrugList'],
    mounted() {},
    methods:{
        addDrug(){
            this.loading = true;
            this.editMode = false;
            this.drug = {};
            $('#drugModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#drugModal').modal('hide');
            this.$emit('refreshDrugList');
        },
        deleteDrug(id){
            Swal.fire({
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
                    this.form.delete('/api/emr/hims/drugs/'+id)
                    .then(response=>{
                        Swal.fire('Deleted!', 'Drug has been deleted.', 'success');
                        this.refresh(response)   
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials(page=1){
            this.$Progress.start();
            axios.get('/api/emr/hims/drugs?page='+page).then(response =>{
                this.refresh(response);
                this.$Progress.finish();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Price List were not loaded successfully',
                })
            });
        },
        refresh(response){
            this.drugs = response.data.drugs;
        },

        updateDrug(drug){
            this.loading = true;
            this.editMode = true;
            this.drug = drug;
            $('#drugModal').modal('show');
            this.loading = false;
        }
    },
    props: {
        drugs: Array,
    }
}
</script>