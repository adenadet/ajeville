<template>
<div class="container-fluid">
    <div class="modal fade" id="contactModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title" v-html="editMode ? 'Edit Contact' : 'Create Contact'"></h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button></div>
                <div class="modal-body"><EMRPatientFormContact :editMode="editMode" :contact="contact" /></div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-3 col-md-5 col-sm-12">
            <EMRPatientDetailCard :source="source" />
        </div>
        <div class="col-lg-9 col-md-7 col-sm-12 pt-0 p-3">
            <div class="row">
                <div class="col-md-2">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item" v-if="source =='Consultation'"><a class="nav-link" href="#allergies" data-toggle="tab">Allergies</a></li>
                        <li class="nav-item"><a class="nav-link active" href="#bio-data" data-toggle="tab">Bio Data</a></li>
                        <li class="nav-item" v-if="source =='consultation'"><a class="nav-link" href="#consultations" data-toggle="tab">Consultations</a></li>
                        <li class="nav-item" v-if="source != 'consultation'"><a class="nav-link" href="#contacts" data-toggle="tab">Contacts</a></li>
                        <li class="nav-item"><a class="nav-link" href="#insurances" data-toggle="tab">Insurances</a></li>
                        <li class="nav-item"><a class="nav-link" href="#laboratory" data-toggle="tab">Laboratory </a></li>
                        <li class="nav-item" v-if="source !='consultation'"><a class="nav-link" href="#next-of-kin" data-toggle="tab">Next of Kin</a></li>
                        <li class="nav-item"><a class="nav-link" href="#radiology" data-toggle="tab"> Radiology</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tasks" data-toggle="tab">Transactions</a></li>
                        <li class="nav-item" v-if="source !='consultation'"><a class="nav-link" href="#password" data-toggle="tab">Reset Password</a></li>
                    </ul>
                </div>
                <div class="card col-md-10  p-0">
                    <div class="tab-content" v-if="patient.user != null">
                        <div class="tab-pane active" id="bio-data">
                            <EMRPatientDetailBioData />
                        </div>
                        <div class="tab-pane" id="consultations" v-if="source =='consultation'">
                            Put previous consultations list here
                            <!--EMRConsultantDetailBioData /-->
                        </div>
                        <div class="tab-pane" id="next-of-kin" v-if="source !='consultation'">
                            <EMRPatientDetailNextOfKin />
                        </div>
                        <div class="tab-pane" id="allergies" v-if="source == 'consultation'">
                            <EMRPatientDetailAllergies :patient="patient" />
                        </div>
                        <div class="tab-pane p-0" id="insurances">
                            <EMRPatientDetailInsurances />
                        </div>
                        <div class="tab-pane p-0" id="laboratory" v-if="source == 'consultation'">
                            <EMRLaboratoryDetailRequestList type="patient"/>
                        </div>
                        <div class="tab-pane p-0" id="radiology" v-if="source == 'consultation'">
                            <EMRRadiologyDetailRequestList type="patient"/>
                        </div>
                        <div class="tab-pane p-0" id="radiology" v-if="source == 'consultation'">
                            <EMRPharmacyDetailPrescriptionList type="patient"/>
                        </div>
                        <div class="tab-pane p-0" id="contacts" v-if="source != 'consultation'">
                            <div class="card-header bg-dark">
                                <h3 class="card-title">List of Contact(s)</h3>
                                <div class="card-tools"><button type="button" @click="addContact()" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i></button></div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-striped table-hover text-nowrap">
                                <thead><tr><th>Name</th><th>Address</th><th>Phone Number</th><th>Email</th><th></th></tr></thead>
                                <tbody>
                                    <tr v-for="contact in patient.contacts" :key="contact.id" >
                                    <td>{{contact.name}}</td>
                                    <td v-html="contact.address"></td>
                                    <td>{{contact.phone+(contact.alt_phone != null ? ', '+contact.alt_phone : '')}}</td>
                                    <td>{{contact.email_address}}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-primary" @click="editContact(contact)"><i class="fa fa-edit"></i></button>
                                        </div>
                                    </td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="password">
                            <div class="card-body"><!--EMRPatientFormPassword :patient="patient.user" /--></div>
                        </div>
                        <div class="tab-pane" id="tasks">
                            <!--FinanceDetailPatientTransactions /-->
                        </div>
                    </div>
                    <div class="tab-content" v-else>
                        Loading...
                    </div>
                </div>
            </div>
        </div>
    </div>                           
</div>
</template>
<script>
export default {
    computed:{
        patient(){
            var patient = this.$store.getters.currentPatient;
            return patient;
        },
        visit(){
            var visit = this.$store.getters.currentVisit;
            return visit;
        },
    },
    data(){
        return  {
            allergy: {},
            contact: {},
            contacts: [],
            editMode: true, 
            nations: [],  
            user:{},
            transactions: {}, 
        }
    },
    mounted() {
        /*Fire.$on('pageReload', () => {
            this.closeModal();
        });
        Fire.$on('patientReset', () => {
            this.reloadPatient();
        });*/
    },
    methods:{
        addAllergy(){
            this.loading = true;
            this.editMode = false;
            let details = {'allergy': {}, 'patient':this.user};
            this.allergy = details;
            $('#allergyModal').modal('show');
            this.loading = false;
        },
        addContact(){
            this.loading = true;
            this.editMode = false;
            let details = {'patient': this.patient, 'contact': {}};
            Fire.$emit('ContactDataFill', details);
            $('#contactModal').modal('show');
            this.loading = false;
        },
        closeModal(){
            $('#allergyModal').modal('hide');
            $('#contactModal').modal('hide');
            $('#contactModal').modal('hide');
        },
        editAllergy(allergy){
            this.loading = true;
            this.editMode = true;
            let details = {'allergy': allergy, 'patient':this.user};
            //Fire.$emit('AllergyDataFill', (details));
            $('#allergyModal').modal('show');
            this.loading = false;
        },
        editContact(contact){
            this.loading = true;
            this.editMode = true;
            let details = {'patient': this.patient, 'contact': contact};
            //Fire.$emit('ContactDataFill', details);
            $('#contactModal').modal('show');
            this.loading = false;
        },
        reloadPatient(response){
            //Fire.$emit('BioDataFill', this.patient.user);
            //Fire.$emit('NextOfKinFill', {'nok': this.patient.user.next_of_kin, 'user_id': this.patient.user.id}); 
            //Fire.$emit('refreshPatientData', this.patient.id);
        },
    },
    props:{
        source: String,
    }
}
</script>