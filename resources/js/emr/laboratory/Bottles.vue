<template>
    <section class="container-fluid">
        <div class="modal fade" id="bottleFormModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title">Bottle Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <EMRLaboratoryFormBottle :bottle.sync="bottle" :editMode="editMode" @refreshBottleForm="getInitials"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Laboratory Bottles</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Additive</th>
                                    <th>Colour</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th><button class="btn btn-xs btn-primary" @click="createBottle()"><i class="fa fa-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(bottle, index) in bottles.data" :key="bottle.id">
                                    <td>{{addOne(index)}}</td>
                                    <td>{{bottle.name}}</td>
                                    <td>{{ bottle.additive }}</td>
                                    <td>{{ bottle.colour }}</td>
                                    <td v-html="bottle.description"></td>
                                    <td>
                                        <span v-if="bottle.status == 1" class="badge badge-success">Active</span>
                                        <span v-if="bottle.status == 0" class="badge badge-danger">Inactive</span>
                                    </td>
                                    <td>
                                        <span class="nav-link" data-toggle="dropdown" href="#">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </span>
                                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                            <button class="btn btn-block dropdown-item" @click="editBottle(bottle)"><i class="fas fa-edit mr-2 text-primary"></i> Edit Bottle Type</button>
                                            <button class="btn btn-block dropdown-item" @click="deactivateBottle(bottle.id)"><i class="fas fa-power-off mr-2"></i> Deactivate Bottle Type</button>
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
import EMRLaboratoryFormBottle from '@/emr/laboratory/forms/Bottle.vue';
export default {
    components:{EMRLaboratoryFormBottle},    data() {
        return {
            bottles: {},
            bottle: {},
            current_page: 1,
            editMode: true,
            form: new Form({}),
            query: '',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        createBottle(){
            this.loading = true;
            this.editMode = false;
            this.bottle = {};
            $('#bottleFormModal').modal('show');
            this.loading = false;
        },
        deactivateBottle(id){
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
                    this.form.delete('/api/emr/laboratory/bottles/'+id)
                    .then(response=>{
                        this.$emit('')
                        this.$swal.fire('Deleted!', 'Bottle has been deactivated/reactivated.', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editBottle(bottle){
            this.loading = true;
            this.editMode = true;
            this.bottle = bottle;
            $('#bottleFormModal').modal('show');
            this.loading = false;
        },
        getInitials(){
            this.loading = true; 
            axios.get('/api/emr/laboratory/bottles?page='+this.current_page+'query='+this.query)
            .then(response => {
                this.bottles = response.data.bottles;
                $('#bottleFormModal').modal('hide');
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your bottles did not loaded successfully',})
            })
            .finally(()=>{
                this.loading = false;
            });
        },
    },
}
</script>