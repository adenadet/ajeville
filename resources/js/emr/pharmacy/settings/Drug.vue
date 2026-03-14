<template>
<section class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-widget">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-paint-brush text-primary"></i> {{ drug.name }}</h3></div>
                <div class="card-body">
                    <p class="text-muted">{{ drug.description }}</p>
                    <br>
                    <div class="text-muted">
                        <p class="text-sm">High Alert Drug
                            <b class="d-block">
                                <i v-if="drug.ham == 0" class="fa fa-square"></i>
                                <i v-else class="fa fa-check"></i>
                            </b>
                        </p>
                        <p class="text-sm">Last Updated By:
                            <b class="d-block">{{ drug.creator | fullName }}</b>
                        </p>
                    </div>

                    <div class="text-center mt-5 mb-3">
                        <button class="btn btn-sm btn-primary" @click="editDrug(drug)"><i class="fa fa-edit mr-1"></i> Update Drug</button>
                        <button class="btn btn-sm btn-danger" @click="deactivateDrug(drug.id)"><i class="fa fa-trash mr-1"></i> Deactivate</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Specific Drugs </h3>

                    <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 150px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Form</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(specific_drug, index) in drug.specific_drugs">
                                <td>{{ index | addOne }}</td>
                                <td>{{ specific_drug.name}}</td>
                                <td>{{ specific_drug.drug_form }}</td>
                                <td>{{ specific_drug.status }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
data(){
    return  {
        drug: {},
        editMode: false,
    }
},
mounted() {
    this.getAllInitials();
    Fire.$on('SpecificDrugDataFill', service =>{
        this.specificDrugData.fill(service);
    });
},
methods:{
    createSpecificDrug(){
        this.$Progress.start();
        this.specificDrugData.post('/api/emr/hims/services')
        .then(response =>{
            Fire.$emit('SpecificDrugRefresh', response);
            Swal.fire({
                icon: 'success',
                title: 'The SpecificDrug '+ this.specificDrugData.name+' has been created',
                showConfirmButton: false,
                timer: 1500
            });
        })
        .catch(()=>{
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!',
                footer: 'Please try again later!'
            });
            this.$Progress.fail();
        });
        this.$Progress.finish();
        this.specificDrugData.clear();
    },

    updateSpecificDrug(){
        this.$Progress.start();
        this.specificDrugData.put('/api/emr/hims/services/'+ this.specificDrugData.id)
        .then(response =>{
            Fire.$emit('SpecificDrugRefresh', response);
            Swal.fire({
                icon: 'success',
                title: 'The SpecificDrug '+this.specificDrugData.name+' has been updated',
                showConfirmButton: false,
                timer: 1500
            });
            this.$Progress.finish();
            this.specificDrugData.clear();
        })
        .catch(()=>{
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!',
                footer: 'Please try again later!'
            });
            this.$Progress.fail();
        });            
    },
    getAllInitials(){
        this.$Progress.start();
        axios.get('/api/emr/hims/drugs/'+this.$route.params.id).then(response =>{
            this.drug = response.data.drug
            this.$Progress.finish();
        })
        .catch(()=>{
            this.$Progress.fail();
            toast.fire({
                icon: 'error',
                title: 'Price List were not loaded successfully',
            })
        });
    }
},
props:{ editMode: Boolean,}
}
</script>