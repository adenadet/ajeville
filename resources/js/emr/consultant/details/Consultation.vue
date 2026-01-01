<template>
<section class="container-fluid">
    <div class="modal fade" id="modal-lg">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Modify Consultation</h4>
                    <button type="button" class="close" @click="closeModal()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <EMRConsultantFormConsultation :consultation="consultation" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h4 class="card-title">
                        Consultation - {{ consultation.unique_id }}
                    </h4>
                    <div class="card-tools">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-default" @click="editConsultation()"><i class="fa fa-edit mr-1"></i> Edit</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="ribbon-wrapper ribbon-xl">
                        <div class="ribbon bg-warning">
                            Admission Advised
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9">
                            <EMRVisitDetailSummary />
                        </div>
                        <div class="col-md-3">

                        </div>
                        <div class="col-md-4">
                            Current Medication List
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-dark">
                                    Presenting Complaint
                                </div>
                                <div class="card-body" v-html="consultation.complaint"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-dark">
                                    Complaint History
                                </div>
                                <div class="card-body" v-html="consultation.history"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-dark">
                                    Initial Diagnosis 
                                </div>
                                <div class="card-body">
                                    <ul>
                                        <li v-for="diagnosis in initial_diagnosis">
                                            {{diagnosis.name}} - [{{ diagnosis.code }}] 
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-dark">
                                    Final Diagnosis 
                                </div>
                                <div class="card-body">
                                    <ul>
                                        <li v-for="diagnosis in final_diagnosis">
                                            {{diagnosis.name}} - [{{ diagnosis.code }}] 
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-dark">
                                    <h3 class="card-title">Further Actions</h3>
                                </div>
                                <div class="card-body">
                                    <div id="accordion">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title w-100">
                                                    <a class="d-block w-100" data-toggle="collapse" href="#collapseOne">
                                                        Prescriptions
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapseOne" class="collapse show" data-parent="#accordion">
                                                <div class="card-body p-0">
                                                    <div class="row" v-for="(prescription, index) in consultation.prescriptions" :key="prescription.id">
                                                        <div class="col-md-12">
                                                            <div class="card p-0">
                                                                <div class="card-header">
                                                                    <h4 class="card-title">Prescription: {{ prescription.unique_id}}</h4>
                                                                    <div class="card-tools">
                                                                        <div class="btn-group">
                                                                            <button class="btn btn-sm bg-dark"><i class="fa fa-edit mr-1"></i>Edit</button>
                                                                            <button class="btn btn-sm btn-danger" @click="removePrescription(prescription.id)"><i class="fa fa-trash mr-1"></i>Delete</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body p-0">
                                                                    <div class="table-responsive p-0">
                                                                        <table class="table table-hover text-nowrap">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>#</th>
                                                                                    <th>Drug</th>
                                                                                    <th>Specific Drug</th>
                                                                                    <th>Dose</th>
                                                                                    <th>Form</th>
                                                                                    <th>Quantity</th>
                                                                                    <th>Duration</th>
                                                                                    <th>Frequency</th>
                                                                                    <th>Route</th>
                                                                                    <th></th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr v-for="(drug, index) in prescription.drugs" :key="drug.id">
                                                                                    <td>{{ index | addOne }}</td>
                                                                                    <td>{{ drug.drug_name }}</td>
                                                                                    <td>{{ drug.specific_drug != null ? drug.specific_drug.name : '' }}
                                                                                    </td>
                                                                                    <td>{{ drug.dose }}</td>
                                                                                    <td>{{ drug.form }}</td>
                                                                                    <td>{{ drug.quantity }}</td>
                                                                                    <td>{{ drug.duration }}</td>
                                                                                    <td>{{ drug.frequency }}</td>
                                                                                    <td>{{ drug.route }}</td>
                                                                                    <td>
                                                                                        <span class="nav-link" data-toggle="dropdown" href="#">
                                                                                            <i class="fa fa-ellipsis-v"></i>
                                                                                        </span>
                                                                                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                                                            <button class="btn btn-block  dropdown-item" @click="editDrug(drug)"><i class="fas fa-edit text-primary mr-2"></i> Edit</button>
                                                                                            <button class="btn btn-block dropdown-item" @click="removeDrug(drug.id)"><i class="fas fa-trash text-danger mr-2"></i> Remove</button>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">
                                                    <a class="d-block" data-toggle="collapse" href="#collapseTwo">Laboratory Investigations</a>
                                                </h4>
                                                <div class="card-tools">
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm bg-dark"><i class="fa fa-plus mr-2"></i> Add More</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="collapseTwo" class="collapse" data-parent="#accordion">
                                                <div class="card-body table-responsive p-0">
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
                                                            <tr v-for="(request, index) in consultation.laboratory" :key="request.id">
                                                                <td>{{ request.item != null ? request.item.name : 'Old Item' }}</td>
                                                                <td>
                                                                    <div class="form-control" v-html="request.quantity"></div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-control" v-html="request.description"></div>
                                                                </td>
                                                                <td>
                                                                    <span class="nav-link" data-toggle="dropdown" href="#">
                                                                        <i class="fa fa-ellipsis-v"></i>
                                                                    </span>
                                                                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                                        <button v-if="request.status == 0" class="btn btn-block  dropdown-item" @click="editLaboratory(request)"><i class="fas fa-edit text-primary mr-2"></i> Edit</button>
                                                                        <button v-if="request.status == 0" class="btn btn-block dropdown-item" @click="removeLaboratory(request.id)"><i class="fas fa-trash text-danger mr-2"></i> Remove</button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">
                                                    <a class="d-block" data-toggle="collapse" href="#collapseThree">Radiology Investigations</a>
                                                </h4>
                                                <div class="card-tools">
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm bg-dark"><i class="fa fa-plus mr-2"></i> Add More</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="collapseThree" class="collapse" data-parent="#accordion">
                                                <div class="card-body table-responsive p-0">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Name </th>
                                                                <th>Quantity </th>
                                                                <th>Description</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(item, index) in  consultation.radiology" :key="item.id">
                                                                <td>{{ item.test.name }}</td>
                                                                <td>
                                                                    <div class="form-control" v-html="item.quantity"></div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-control" v-html="item.description"></div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import { ModelListSelect } from 'vue-search-select';

export default {
    components: {
        ModelListSelect
    },
    computed: {
        initial_diagnosis(){
            if (this.consultation != null && this.consultation.initial_diagnosis != null){return JSON.parse(this.consultation.initial_diagnosis);}
            else{return null;}
        },
        final_diagnosis(){
            if (this.consultation != null && this.consultation.final_diagnosis != null){return JSON.parse(this.consultation.final_diagnosis);}
            else{return null;}
        },
    },
    data() {
        return {
            branches: [],
            consultation: {},
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
    },
    methods: {
        addItem(){
            var item = this.services.find(item => item.id === this.investigation);
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
        createLaboratory() {
            this.$Progress.start();
            this.RadiologyForm.visit_id = this.visit.id;
            this.RadiologyForm.post('/api/emr/hims/radiology')
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
        editConsultation(){

        },
        editLaboratory(request){

        },
        
        getAllInitials() {
            this.loading = true;
            axios.get('/api/emr/consultations/consultants/'+this.$route.params.id).then(response => {
                this.refresh(response);
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Visit Form was not loaded successfully',
                })
            });
        },
        removeItem(index){
            this.RadiologyForm.investigations.splice(index, 1);
        },
        removeLaboratory(id){
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
                    this.Form.delete('/api/emr/hims/laboratory/'+id)
                    .then(response=>{
                        Swal.fire('Deleted!', 'Prescription has been deleted.', 'success');
                        this.getAllInitials();  
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        removePrescription(id){
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
                    this.Form.delete('/api/emr/hims/prescriptions/'+id)
                    .then(response=>{
                        Swal.fire('Deleted!', 'Prescription has been deleted.', 'success');
                        this.getAllInitials();  
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        removeRadiology(id){
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
                    this.Form.delete('/api/emr/hims/radiology/'+id)
                    .then(response=>{
                        Swal.fire('Deleted!', response.data.message, response.data.icon);
                        this.getAllInitials();  
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        sortStaff(){},
        refresh(response) {
            this.consultation = response.data.consultation;
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
        
    },
}
</script>