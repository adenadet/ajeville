<template>
    <section class="container-fluid">
        <div class="modal fade" id="analyteFormModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title">Analyte Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <EMRLaboratoryFormAnalyte :analyte.sync="analyte" :editMode="editMode" @refreshAnalyteForm="getInitials"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Laboratory Analytes</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Default Unit</th>
                                    <th>Input Type</th>
                                    <th>Option</th>
                                    <th>Status</th>
                                    <th><button class="btn btn-xs btn-primary" @click="createAnalyte()"><i class="fa fa-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(analyte, index) in analytes.data" :key="analyte.id">
                                    <td>{{addOne(index)}}</td>
                                    <td>{{analyte.name}}</td>
                                    <td v-html="readMore(analyte.description, 50, '...')"></td>
                                    <td>{{ analyte.default_unit }}</td>
                                    <td>{{ analyte.input_type }}</td>
                                    <td v-html="analyte.options"></td>
                                    <td>
                                        <span v-if="analyte.deleted_at == null" class="badge badge-success">Active</span>
                                        <span v-else class="badge badge-danger">Inactive</span>
                                    </td>
                                    <td>
                                        <span class="nav-link" data-toggle="dropdown" href="#">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </span>
                                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                            <router-link class="btn btn-block dropdown-item" :to="'/emr/laboratory/settings/analytes/'+analyte.id"><i class="fas fa-eye mr-2 text-primary"></i> See Reference Ranges</router-link> 
                                            <button class="btn btn-block dropdown-item" @click="editAnalyte(analyte)"><i class="fas fa-edit mr-2 text-primary"></i> Edit Analyte Type</button>
                                            <button class="btn btn-block dropdown-item" @click="deactivateAnalyte(analyte.id)"><i class="fas fa-power-off mr-2"></i> Deactivate Analyte Type</button>
                                        </div>
                                    </td>  
                                </tr>    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import EMRLaboratoryFormAnalyte from '@/emr/laboratory/forms/Analyte.vue';

export default {
    components:{EMRLaboratoryFormAnalyte},
    data() {
        return {
            analytes: {},
            analyte: {},
            current_page: 1,
            editMode: true,
            form: new Form({}),
            loading: false,
            query: '',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        createAnalyte(){
            this.loading = true;
            this.editMode = false;
            this.analyte = {};
            $('#analyteFormModal').modal('show');
            this.loading = false;
        },
        deactivateAnalyte(id){
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
                    this.form.delete('/api/emr/laboratory/analytes/'+id)
                    .then(response=>{
                        this.$emit('')
                        this.$swal.fire('Deleted!', 'Analyte has been deactivated/reactivated.', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editAnalyte(analyte){
            this.loading = true;
            this.editMode = true;
            this.analyte = analyte;
            $('#analyteFormModal').modal('show');
            this.loading = false;
        },
        getInitials(){
            this.loading = true; 
            axios.get('/api/emr/laboratory/analytes?page='+this.current_page+'query='+this.query)
            .then(response => {
                this.analytes = response.data.analytes;
                $('#analyteFormModal').modal('hide');
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your analytes did not loaded successfully',})
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        viewReferenceRange(analyte){
            this.loading = true;
            axios.get('/api/emr/laboratory/reference_ranges?page='+this.current_page+'query='+this.query)
            .then(response => {
                this.analytes = response.data.analytes;
                $('#analyteFormModal').modal('hide');
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your analytes did not loaded successfully',})
            })
            .finally(()=>{
                this.loading = false;
            });
        }
    },
}
</script>