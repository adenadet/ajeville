<template>
<section class="card">
    <form class="card-body">
        <alert-error :form="InvestigationForm"></alert-error>
        <div class="row">
            <div class="col-sm-12">
                <HimsVisitSummary :visit="visit" />
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Investigation Type</label>
                            <select class="form-control" name="investigation_type_id" id="investigation_type_id" v-model="investigation_type" @change="sortStaff()">
                                <option value="">--Investigation Type--</option>
                                <option value="Laboratory">Laboratory</option>
                                <option value="Radiology">Radiology</option>
                            </select>
                            <has-error :form="InvestigationForm" field="consultant_id"></has-error>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{investigation_type == 'Laboratory' ?'Laboratory Service' : 'Radiology Service'}}</label>
                            <model-list-select v-if="investigation_type == 'Laboratory'" class="form-control" :list="laboratory_services" v-model="investigation" option-value="id" :custom-text="categoryAndName" placeholder="Search for services" />
                            <model-list-select v-else-if="investigation_type == 'Radiology'" class="form-control" :list="radiology_services" v-model="investigation" option-value="id" :custom-text="categoryAndName" placeholder="Search for services" />    
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button class="btn btn-success btn-sm" type="button" @click="addItem()" :disabled="investigation == ''">Add</button>
                    </div>
                </div>
                
            </div>
            <div class="col-sm-8">
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
                        <tr v-for="(item, index) in InvestigationForm.investigations" :key="item.id">
                            <td>{{ item.name}}</td>
                            <td><input class="form-control" type="number" v-model="InvestigationForm.investigations[index].quantity"/></td>
                            <td><textarea class="form-control"  v-model="InvestigationForm.investigations[index].description"></textarea></td>
                            <td><button class="btn btn-xs btn-danger" type="button" @click="removeItem(index)"><i class="fa fa-trash"></i></button> </td>
                        </tr>
                    </tbody>
                </table>
                <button @click.prevent="editMode ? updateInvestigation() : createInvestigation()" type="submit" name="submit" class="float-right mt-2 submit btn btn-primary btn-sm">Submit</button>
        
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
            investigation: '',
            investigation_type: '',
            InvestigationForm: new Form({
                patient_id: '',
                visit_id: '',
                id: '',
                investigations: [],
            }),
            visit: {},
            laboratory_services: [],
            radiology_services: [],
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addItem(){
            //Get the correct Item to be added 
            if (this.investigation_type == 'Laboratory'){
                var item = this.laboratory_services.find(item => item.id === this.investigation); 
            }
            else if (this.investigation_type == 'Radiology'){
                var item = this.radiology_services.find(item => item.id === this.investigation); 
            }

            // Check if the item has already been added
            var index = this.InvestigationForm.investigations.map(function(o) { return o.id; }).indexOf(this.investigation);

            if (index < 0){
                this.InvestigationForm.investigations.push({id: item.id, category_id:item.category_id, description: '', name: item.name, quantity: 1, service_id:item.service_id,})
            }
            else{
                this.InvestigationForm.investigations[index].quantity++;
            }
            this.investigation = '';
        },
        categoryAndName(item) {
            return `${item.category != null ? item.category.name : ''} | ${item.name}`;
        },
        createInvestigation() {
            this.$Progress.start();
            this.InvestigationForm.visit_id = this.visit.id;
            this.InvestigationForm.post('/api/emr/hims/investigations')
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
        },
        getAllInitials() {
            this.$Progress.start();
            axios.get('/api/emr/hims/investigations/initials/'+this.$route.params.id).then(response => {
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
            this.InvestigationForm.investigations.splice(index, 1);
        },
        sortStaff(){},
        refresh(response) {
            this.laboratory_services = response.data.laboratory_services;
            this.radiology_services = response.data.radiology_services;
            this.visit = response.data.visit;
        },
    },
    props: {
        patient: Object,
        editMode: Boolean,
    }
}
</script>