<template>
<section class="container-fluid border-1">
    <form class="card-body">
        <alert-error :form="Form"></alert-error>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Radiology Service</label>
                    <model-list-select class="form-control" :list="services" v-model="investigation" option-value="id" option-text="name" />
                </div>
            </div>
            <div class="col-md-6">
                <label class="text-white">Add</label><br />
                <button class="btn btn-success btn-sm" type="button" @click="addItem()" :disabled="investigation == ''">Add</button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name </th>
                            <th>Quantity </th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in RadiologyForm.investigations" :key="item.id">
                            <td>{{ item.name}}</td>
                            <td><input class="form-control" type="number" v-model=" RadiologyForm.investigations[index].quantity"/></td>
                            <td><textarea class="form-control"  v-model=" RadiologyForm.investigations[index].description"></textarea></td>
                            <td><button class="btn btn-xs btn-danger" type="button" @click="removeItem(index)"><i class="fa fa-trash"></i></button> </td>
                        </tr>
                    </tbody>
                </table>
                <button @click.prevent="editMode ? updateLaboratory() : createLaboratory()" type="submit" name="submit" class="float-right mt-2 submit btn btn-primary btn-sm">Submit</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
import { ModelListSelect } from 'vue-search-select';

export default {
    components: {
        ModelListSelect
    },
    data() {
        return {
            branches: [],
            Form: new Form({}),
            investigation: '',
            investigation_type: '',
            RadiologyForm: new Form({
                patient_id: '',
                visit_id: '',
                consultation_id: '',
                id: '',
                investigations: [],
            }),
            services: [],
        }
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('createConsultation', ()=>{
            this.RadiologyForm.consultation_id = this.consultation.id;
            this.createRadiology();
        });
    },
    methods: {
        addItem(){
            //Get the correct Item to be added 
            var item = this.services.find(item => item.id === this.investigation);

            // Check if the item has already been added
            var index = this.RadiologyForm.investigations.map(function(o) { return o.id; }).indexOf(this.investigation);

            if (index < 0){
                this.RadiologyForm.investigations.push({id: item.id, category_id:item.category_id, description: '', name: item.name, quantity: 1, service_id:item.service_id,})
            }
            else{
                this.RadiologyForm.investigations[index].quantity++;
            }
            this.investigation = '';
        },
        categoryAndName(item) {
            return `${item.category != null ? item.category.name : ''} | ${item.name}`;
        },
        createRadiology() {
            this.$Progress.start();
            this.RadiologyForm.visit_id = this.visit.id;
            this.RadiologyForm.post('/api/emr/hims/radiology')
            .then(response => {
                //this.$Progress.finish();
                //this.$router.push('/hims/visits');
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
        },
        getAllInitials() {
            this.$Progress.start();
            axios.get('/api/emr/hims/radiology/initials').then(response => {
                this.refresh(response);
                this.$Progress.finish();
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Visit Form was not loaded successfully',
                })
            });
        },
        removeItem(index){
            alert(index);
            this.RadiologyForm.investigations.splice(index, 1);
        },
        sortStaff(){},
        refresh(response) {
            this.services = response.data.services;
        },
        updateLaboratory(){
            this.$Progress.start();
            this.RadiologyForm.visit_id = this.visit.id;
            this.RadiologyForm.post('/api/emr/hims/radiology/'+this.consultation.id)
            .then(response => {
                this.$Progress.finish();
                this.$router.push('/hims/visits');
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
        }
    },
    props: {
        consultation: Object,
        patient: Object,
        editMode: Boolean,
        visit: Object,
    },
}
</script>