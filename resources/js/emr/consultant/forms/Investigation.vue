<template>
<section>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Investigation</label>
                            <input type="text" v-model="itemForm.service_name" class="form-control" id="serviceName" name="serviceName" placeholder="service Name" autocomplete="off" @keyup="searchservices" @focus="modal = true" />
                            <input type="hidden" v-model="itemForm.service_id" name="service_id" id="service_id" autocomplete="off" />
                            <div class="row" v-if="services != null && modal == true" style="z-index: 1070; position:absolute;">
                                <ul class="col-md-10 offset-1 bg-gray  text-white">
                                    <li v-for="drug in services" @click="setService(service)" class="border-bottom p-2" style="cursor: pointer">{{ service.name }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Dose</label>
                            <input type="text" v-model="itemForm.dose" class="form-control" id="dose" name="dose" placeholder="service Dose" autocomplete="off"/>
                        </div>
                        <div class="col-md-3">
                            <label>Form</label>
                            <select v-model="itemForm.form" class="form-control" id="form" name="form" autocomplete="off">
                                <option value="">--Select Form--</option>
                                <option v-for="form in serviceForms" :key="form.name" :value="form.name">{{form.name}}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Duration</label>
                            <input type="text" v-model="itemForm.duration" class="form-control" id="duration" name="duration" placeholder="Duration" autocomplete="off" />
                        </div>
                        <div class="col-md-3">
                            <label>Frequency</label>
                            <select v-model="itemForm.frequency" class="form-control" id="frequency" name="frequency" autocomplete="off">
                                <option value="">--Select Frequency--</option>
                                <option v-for="frequency in frequencies" :key="frequency.name" :value="frequency.name">{{frequency.name}}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Route</label>
                            <select v-model="itemForm.route" class="form-control" id="route" name="route" autocomplete="off">
                                <option value="">--Select Route--</option>
                                <option v-for="route in routes" :key="route.id" :value="route.id">{{route.name}}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Quantity</label>
                            <input type="text" v-model="itemForm.quantity" class="form-control" id="quantity" name="quantity" placeholder="Quantity" autocomplete="off"/>
                        </div>
                        <div class="col-md-3">
                            <label>Detail</label>
                            <textarea v-model="itemForm.detail" class="form-control" id="details" name="details" placeholder="Details" autocomplete="off"/>
                        </div>
                    </div>
                    <!--button class="mt-5 btn btn-sm btn-primary" type="button" @click="addDrug()">Add</button-->
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Drug</th>
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
                            <tr v-for="(service, index) in investigationForm.services" :key="service.id">
                                <td>{{ index |addOne }}</td>
                                <td>{{ service.service_name }}</td>
                                <td>{{ service.dose }}</td>
                                <td>{{ service.form }}</td>
                                <td>{{ service.quantity }}</td>
                                <td>{{ service.duration }}</td>
                                <td>{{ service.frequency }}</td>
                                <td>{{ service.route }}</td>
                                <td><div class="btn-group"><button class="btn btn-sm btn-default" @click=removeDrug(service)><i class="fa fa-trash"></i></button></div></td>
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
export default {
    data() {
        return {
            doctors: [],
            doctor: {},
            serviceName: '',
            serviceId: '',
            services: [],
            modal: false,
            serviceForms: [],
            itemForm: new Form({
                description: '',
                detail: '',
                service_id: '',
                service_name: '',
                quantity: '',
            }),
            investigationForm: new Form({
                id: '',
                doctor_id: '',
                doctor_name: '',
                start_date: '',
                patient_id: '',
                services: [],
            }),
        }
    },
    methods: {
        addService(){
            let service = {
                service_id: this.itemForm.service_id,
                service_name: this.itemForm.service_name,
                detail: this.itemForm.detail,
                dose: this.itemForm.dose,
                duration: this.itemForm.duration,
                form: this.itemForm.form,
                form_id: this.itemForm.form_id,
                frequency: this.itemForm.frequency,
                route: this.itemForm.route,
                quantity: this.itemForm.quantity,
            }; 
            this.investigationForm.services.push(drug);
            this.itemForm.reset();
            console.log(this.investigationForm.drugs);
        },
        createInvestigation() {
            this.$Progress.start();
            this.investigationForm.post('/api/emr/hims/investigations')
            .then(response => {
                this.$Progress.finish();
                Swal.fire({ icon: 'success', title: 'The Investigation has been created', showConfirmButton: false, timer: 1500 });
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
                this.$Progress.fail();
            });
        },
        getInitials(){
            axios.get('/api/emr/hims/investigations/initials')
            .then((response) => { 
                this.serviceForms = response.data.service_forms; 
                this.frequencies = response.data.frequencies;
                this.routes = response.data.drug_routes; 
            })
            .catch(() => { }); 
        },
        removeService(service){this.investigationForm.services.pop(service);},
        searchServices() {
            axios.get('/api/emr/hims/services/search?q=' + this.itemForm.service_name)
            .then((response) => { this.drugs = response.data.services; })
            .catch(() => { });
        },
        setService(drug) {
            this.itemForm.service_name = service.name;
            this.itemForm.service_id = service.id;
            this.services = [];
            this.modal = false;
        },
        updateInvestigation() {
            this.$Progress.start();
            this.investigationForm.put('/api/emr/hims/investigations/' + this.investigationForm.id)
            .then(response => {
                this.$Progress.finish();
                Fire.$emit('pageReload');
                Swal.fire({ icon: 'success', title: 'The Investigation has been updated', showConfirmButton: false, timer: 1500 });
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
                this.$Progress.fail();
            });
        },
    },
    mounted() {
        this.getInitials();
        Fire.$on('PrescriptionDataFill', details => {
            this.investigationForm.patient_id = details.patient.id;
            if (details.prescription.id != null){
                this.investigationForm.id = details.prescription.id;
                this.investigationForm.doctor_id = details.prescription.doctor_id;
                this.investigationForm.doctor_name = details.prescription.doctor_name;
                this.investigationForm.start_date = details.prescription.start_date;
                this.investigationForm.end_date = details.prescription.end_date;
                this.investigationForm.drugs = details.prescription.drugs;
            }
            else{
                this.investigationForm.id = '';
                this.investigationForm.doctor_id = '';
                this.investigationForm.doctor_name = '';
                this.investigationForm.start_date = '';
                this.investigationForm.end_date = '';
                this.investigationForm.drugs = [];
            }
            alert(this.investigationForm.drugs)
        });
    },
    props: {
        'appointment': Object,
        'consultant': Object,
        'contact': Object,
        'editMode': Boolean,
    },
}
</script>