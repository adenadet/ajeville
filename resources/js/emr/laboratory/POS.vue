<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <PatientFormSearch />
            </div>
            <div class="col-md-4">
                <VisitDetailSummary />
            </div>
            <div class="col-md-8">
                <div class="card card-primary card-outline">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-edit"></i>Create New Request</h3></div>
                    <div class="card-body">
                        <form >    
                            <alert-error :form="requestData"></alert-error>
                            <div class="row">
                                <div class="col-md-12">{{ patient.unique_id }}</div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Laboratory Service</label>
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
                                            <tr v-for="(item, index) in requestData.investigations" :key="item.id">
                                                <td>{{ item.name}}</td>
                                                <td><input class="form-control" type="number" v-model=" requestData.investigations[index].quantity"/></td>
                                                <td><textarea class="form-control"  v-model=" requestData.investigations[index].description"></textarea></td>
                                                <td><button class="btn btn-xs btn-danger" type="button" @click="removeItem(index)"><i class="fa fa-trash"></i></button> </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-12">
                                    <button @click.prevent="editMode ? updateLaboratoryRequest() : createLaboratoryRequest()" type="submit" name="submit" class="float-right mt-2 submit btn btn-primary btn-sm">Submit</button>        
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import { ModelListSelect } from 'vue-search-select';
export default {
    components: {ModelListSelect},
    computed:{
        patient(){
            var patient = this.$store.getters.currentPatient;
            return patient;
        },
        today(){
            return new Date();
        },
        visit(){
            var visit = this.$store.getters.currentVisit;
            return visit;
        },
    },
    data(){
        return {
            investigations: [],
            investigation: '',
            laboratory_services: [],
            requestData: new Form({
                id:'',
                investigations:[], 
                visit_id:[],
                patient_id: [], 
            }),
            patients: [],
            patient_visits: [],
            prev_cat: true,
            prev_category: {},
            services: [],
            sub_categories: [],
        }
    },
    methods:{
        addItem(){
            //Get the correct Item to be added 
            var item = this.services.find(item => item.id === this.investigation);
            // Check if the item has already been added
            var index = this.requestData.investigations.map(function(o) { return o.id; }).indexOf(this.investigation);
            if (index < 0){
                this.requestData.investigations.push({id: item.id, category_id:item.category_id, description: '', name: item.name, quantity: 1, service_id:item.service_id,})
            }
            else{
                this.requestData.investigations[index].quantity++;
            }
            this.investigation = '';
        },
        changeSubCategory(){
            this.prev_cat = false;
            this.requestData.sub_category_id = '';
            this.sub_categories = this.categories[this.requestData.category_id].sub_categories;
        },
        createLaboratoryRequest(){
            this.requestData.patient_id = this.patient.id;
            this.requestData.post('/api/emr/laboratory/requests')
            .then(response=>{
                Fire.$emit('GetCourse', response);
                Swal.fire({
                    icon: 'success',
                    title: response.data.message,
                });
                this.requestData.reset();
                Fire.emit('')
            })
            .catch(()=>{
                this.$Progress.fail();
                Swal.fire({
                    icon: 'error',
                    title: 'Your form was not sent try again later!',
                });
            });
        },
        getInitials(page = 1){
            this.$Progress.start();
            axios.get('/api/emr/laboratory/requests/initials?page='+page)
            .then(response => {
                
                this.laboratory_services = response.data.services;
                this.services = response.data.services;
                this.loading = false;
                this.$Progress.finish();
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        updateLaboratoryRequest(id){
            this.requestData.put('/api/emr/laboratory/requests/'+this.requestData.id)
            .then(response=>{
                Swal.fire({
                    icon: 'success',
                    title: response.data.message,
                });
                this.requestData.reset();
            })
            .catch(()=>{
                this.$Progress.fail();
                Swal.fire({
                    icon: 'error',
                    title: 'Your form was not sent try again later!',
                });
            });
        },
        
    },
    mounted() {
        this.getInitials();
        Fire.$on('lecturerFill', tutors => {
            console.log("Working");
            for (let i = 0; i < tutors.length; i++) {
                this.lecturers.push({
                    'id' : tutors[i].id, 
                    'name' : tutors[i].unique_id+' | '+tutors[i].first_name+' '+tutors[i].last_name,
                    'value' : tutors[i].unique_id,
                });
            }
        });
        Fire.$on('requestDataFill', course =>{
            this.requestData.reset();
            this.requestData.fill(course);
            if ((course.category_id !== null)&&(typeof course.category !== 'undefined')){
                this.prev_category = course.category;
                this.sub_categories = course.category.sub_categories;
                
                }
            else{
                this.requestData.category_id = "";
                this.requestData.sub_category_id = "";
                this.requestData.certificate_type_id = "";
                this.requestData.exam_type_id = "";
            }
        });     
    },
    props: {
        'categories': Array,
        'exam_types': Array,
        'certificate_types': Array,
        'editMode': Boolean,
        'tutors':Array,
        'course': Object,
    },
}
</script>