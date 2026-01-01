<template>
    <section class="container-fluid">
        <div class="row">
            <div class="modal fade" id="drugModal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ editMode ? 'Edit Drug' : 'Create Drug'}}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body p-0">
                            <OperationFormDrug :editMode="editMode" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">&nbsp;</h3>
                        <div class="card-tools">
                            <div class="card-tools">
                                <button class="btn btn-sm btn-primary float-sm-right" @click="addDrug()">Add Drug<i class="fa fa-plus ml-1"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover ">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Name</th>
                                    <th>Specific Drugs</th>
                                    <th>High Alert Medication</th>
                                    <th>Description</th>
                                    <th style="width: 40px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(drug, index) in drugs.data">
                                    <td>{{ index | addOne}}</td>
                                    <td>{{ drug.name }} </td>
                                    <td>{{ drug.specific_drugs != null ? drug.specific_drugs.length : 'No Drug item linked' }}</td>
                                    <td>{{ drug.ham == 1 ? 'True' : 'False' }}</td>
                                    <td>{{ drug.description | readMore(25, '...') }}</td>
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
                    </div>
                    <div class="card-footer">
                    <pagination :data="drugs" @pagination-change-page="getInitials">
                        <span slot="prev-nav">&lt; Previous </span>
                        <span slot="next-nav">Next &gt;</span>
                    </pagination>
                </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data(){
        return  {
            drugs: {},
            drug: {},
            editMode: false,
            form: new Form({})
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        addDrug(){
            this.$Progress.start();
            this.editMode = false;
            this.drug = {};
            Fire.$emit('DrugDataFill', {});
            $('#drugModal').modal('show');
            this.$Progress.finish();
        },
        closeModal(){
            $('#drugModal').modal('hide');
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
            this.$Progress.start();
            this.editMode = true;
            this.drug = drug;
            Fire.$emit('DrugDataFill', drug);
            $('#drugModal').modal('show');
            this.$Progress.finish();
        }
    },
    props: {
        
    }
}
</script>