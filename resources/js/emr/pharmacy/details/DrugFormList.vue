<template>
<section class="container-fluid">
    <div class="modal fade" id="drugFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ editMode ? 'Edit Drug' : 'Create Drug'}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRPharmacyFormDrugForm :editMode="editMode" :drug_form.sync="drug_form" @refreshFormDrugForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>            
    <table class="table table-striped table-hover ">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th><button class="btn btn-primary btn-xs" @click="addDrug" type="button"><i class="fa fa-plus"></i></button></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(drug_form, index) in drug_forms">
                <td>{{ addOne(index)}}</td>
                <td>{{ drug_form.name }} </td>
                <td>{{ readMore(drug_form.description, 25, '...') }}</td>
                <td>
                    <span v-if="drug_form.status == 1" class="badge badge-success">Active</span>
                    <span v-else class="badge badge-danger">Inactive</span>
                </td>
                <td>
                    <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu">
                        <!--router-link class="btn btn-block dropdown-item" :to="'/emr/pharmacy/drug_s/'+drug.id"><i class="fa fa-eye mr-1"></i> View </router-link-->
                        <button class="btn btn-block dropdown-item" @click="updateDrug(drug_form)"><i class="fa fa-edit mr-1 text-primary"></i> Edit </button>
                        <button class="btn btn-block dropdown-item" @click="deleteDrug(drug_form.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete</button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
import EMRPharmacyFormDrugForm from '@/emr/pharmacy/forms/DrugForm.vue';
export default {
    components:{EMRPharmacyFormDrugForm},
    data(){
        return  {
            drug_form: {},
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits: ['refreshDrugFormList'],
    mounted() {},
    methods:{
        addDrugForm(){
            this.loading = true;
            this.editMode = false;
            this.drug_form = {};
            $('#drugFormModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#drugFormModal').modal('hide');
            this.$emit('refreshDrugFormList');
        },
        deleteDrugForm(id){
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
                    this.form.delete('/api/emr/hims/drug_forms/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Drug has been deleted.', 'success');
                        this.refresh(response)   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        refreshPage(){
            this.closeModal();
            this.$emit('refreshDrugFormList');
        },
        updateDrugForm(drug_form){
            this.loading = true;
            this.editMode = true;
            this.drug_form = drug_form;
            $('#drugFormModal').modal('show');
            this.loading = false;
        }
    },
    props: {
        drug_forms: Array,
    }
}
</script>