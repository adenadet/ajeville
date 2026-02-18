<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="specimenTypeFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Specimen Type Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRLaboratoryFormSpecimenType :specimen_type.sync="specimen_type" :editMode="editMode" @refreshSpecimenTypeForm="getInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">Specimen Types</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                            <div class="input-group-append"><button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button></div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 600px;">
                    <table class="table table-head-fixed text-nowrap">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Storage Temperature</th>
                                <th>Stability Duration</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th><button class="btn btn-xs btn-primary" @click="addSpecimenType()"><i class="fa fa-plus"></i></button></th>
                            </tr>
                        </thead>
                        <tbody v-if="specimen_types.total > 0">
                            <tr v-for="(specimen_type, index) in specimen_types.data" :key="specimen_type">
                                <td>{{ addOne(index) }}</td>
                                <td>{{ specimen_type.name }}</td>
                                <td>{{ specimen_type.storage_temperature || '-'}}<sup>o</sup>C</td>
                                <td>{{ specimen_type.stability_duration || '-' }} hrs</td>
                                <td v-html="readMore(specimen_type.description, 50, '...')" :title="specimen_type.description"></td>
                                <td>
                                    <span v-if="specimen_type.status == 1" class="badge badge-success">Active</span>
                                    <span v-else class="badge badge-danger">Inactive</span>
                                </td>
                                <td>
                                    <span class="nav-link" data-toggle="dropdown" href="#">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                        <button class="btn btn-block dropdown-item" @click="updateSpecimenType(specimen_type)"><i class="fas fa-edit mr-2 text-primary"></i> Edit Specimen Type</button>
                                        <button class="btn btn-block dropdown-item" @click="deactivateSpecimenType(specimen_type.id)"><i class="fas fa-power-off mr-2"></i> Deactivate Specimen Type</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getInitials" :per-page="specimen_types.per_page != null ? specimen_types.per_page : 52" :records="specimen_types.total != null ? specimen_types.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>   
</section>
</template>
<script>
import EMRLaboratoryFormSpecimenType from '@/emr/laboratory/forms/SpecimenType.vue';

export default {
    components:{EMRLaboratoryFormSpecimenType},
    data() {
        return {
            current_page: 1,
            editMode: true,
            form: new Form({}),
            loading: false,
            query: '',
            specimen_type: {},
            specimen_types: {data: [],total: 0,},
            status: "uncollected",
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addSpecimenType(){
            this.loading = true;
            this.editMode = false;
            this.specimen_type = {};
            $('#specimenTypeFormModal').modal('show');
            this.loading = false;
        },
        deactivateSpecimenType(id){
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
                    this.form.delete('/api/emr/laboratory/specimen_types/'+id)
                    .then(response=>{
                        this.getInitials();
                        this.$swal.fire('Deleted!', 'Specimen Type has been deactivated/reactivated.', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials() {
            this.loading = true;
            axios.get('/api/emr/laboratory/specimen_types?page='+this.current_page+'&query='+this.query)
            .then(response => {
                $('#specimenTypeFormModal').modal('hide');    
                this.refreshPage(response)
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            })
            .finally(() => {
                this.loading = false;
            });
        },
        refreshPage(response) {
            this.specimen_types = response.data.specimen_types;
        },
        updateSpecimenType(specimen_type){
            this.loading = true;
            this.editMode = true;
            this.specimen_type = specimen_type;
            $('#specimenTypeFormModal').modal('show');
            this.loading = false;
        }
    },
    props: {}
}
</script>