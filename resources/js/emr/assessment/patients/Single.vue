<template>
<div class="container-fluid">
    <div class="modal fade" id="contactModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title" v-html="editMode ? 'Edit Contact' : 'Create Contact'"></h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button></div>
                <div class="modal-body"><HimsFormContact :editMode="editMode" :contact="contact" /></div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-3 col-md-5 col-sm-12">
            <div class="user-profile">
                <div class="widget-content widget-content-area">
                    <div class="text-center user-info">
                        <img :src="(user.image) ? '/img/profile/'+user.image : '/img/profile/default.png'" width="300" height="auto" alt="avatar">
                        <p class=""></p>
                    </div>
                    <div class="user-info-list">
                        <div class="">
                            <ul class="contacts-block list-unstyled">
                                <li class="contacts-block__item">
                                    <i class="fa fa-user" width="24" height="24"></i> {{user.first_name}} {{user.middle_name}} {{user.last_name}}
                                </li>
                                <li class="contacts-block__item">
                                    <i class="fa fa-calendar" width="24" height="24"></i> {{user.dob | ExcelDate}}
                                </li>
                                <li class="contacts-block__item">
                                    <i class="fa fa-map-marker" width="24" height="24"></i> 
                                    <span v-html="user.uk_address"></span>  
                                </li>
                                <li class="contacts-block__item">
                                    <a :href="'mailto:'+user.email"><i class="fa fa-envelope" width="24" height="24"></i> {{user.email}}</a>
                                </li>
                                <li class="contacts-block__item">
                                    <i class="fa fa-phone" width="24" height="24"></i> {{user.phone}} {{user.alt_phone ? ', '+user.alt_phone: ''}} 
                                </li>
                            </ul>
                        </div>                                    
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-7 col-sm-12 pt-0 p-3">
            <div class="row">
                <div class="col-md-2">
                    <ul class="nav nav-pills flex-column">
                    <li class="nav-item"><a class="nav-link active" href="#bio-data" data-toggle="tab">Bio Data</a></li>
                    <li class="nav-item"><a class="nav-link" href="#next-of-kin" data-toggle="tab">Next of Kin</a></li>
                    <li class="nav-item"><a class="nav-link" href="#allergies" data-toggle="tab">Allergies</a></li>
                    <li class="nav-item"><a class="nav-link" href="#prescriptions" data-toggle="tab">Prescriptions</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contacts" data-toggle="tab">Contacts</a></li>
                    <!--<li class="nav-item"><a class="nav-link" href="#administrations" data-toggle="tab">Drug Administration</a></li>-->
                    <li class="nav-item"><a class="nav-link" href="#tasks" data-toggle="tab">Tasks</a></li>
                    <li class="nav-item"><a class="nav-link" href="#plans" data-toggle="tab">Plans</a></li>
                    <li class="nav-item"><a class="nav-link" href="#fluid" data-toggle="tab">Fluid</a></li>
                    <li class="nav-item"><a class="nav-link" href="#password" data-toggle="tab">Password</a></li>
                    </ul>
                </div>
                <div class="card col-md-10">
                    <div class="tab-content">
                        <div class="tab-pane active" id="bio-data">
                            <HimsFormPatient :user="user" :nations="nations"/>
                        </div>
                        <div class="tab-pane" id="next-of-kin">
                            <HimsFormNOK :nok="nok"/>
                        </div>
                        <div class="tab-pane" id="allergies">
                            <HimsPatientAllergies :patient="user" />
                        </div>
                        <div class="tab-pane" id="contacts">
                            <div class="card-header">
                                <h3 class="card-title">List of Contacts</h3>
                                <div class="card-tools"><button type="button" @click="addContact()" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></button></div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-striped table-hover text-nowrap">
                                <thead><tr><th>Name</th><th>Address</th><th>Phone Number</th><th>Email</th><th></th></tr></thead>
                                <tbody>
                                    <tr v-for="contact in user.contacts" :key="contact.id" >
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
                            <HimsFormPassword :patient="user" />
                        </div>
                        <div class="tab-pane" id="prescriptions">
                            <HimsPatientFormPrescription :patient="user" />
                        </div>
                        <!--<div class="tab-pane" id="administrations">
                            <PatientChartAdmin />
                        </div>
                        <div class="tab-pane" id="tasks">
                            <PatientChartTask />
                        </div>
                        <div class="tab-pane" id="plans">
                            <PatientChartPlan />
                        </div>
                        <div class="tab-pane" id="plans">
                            <PatientChartFluid />
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>                           
</div>
</template>
<script>
export default {
    data(){
        return  {
            allergy: {},
            contact: {},
            editMode: true, 
            nok:{},
            nations: [],  
            patient: {},
            user:{}, 
        }
    },
    created() {
        this.getInitials();
        Fire.$on('Reload', response =>{this.refreshProfile(response);});
        Fire.$on('pageReload', () => {
            this.getInitials();
            this.closeModal();
        });
    },
    methods:{
        addAllergy(){
            this.$Progress.start();
            this.editMode = false;
            let details = {'allergy': {}, 'patient':this.user};
            Fire.$emit('AllergyDataFill', (details));
            $('#allergyModal').modal('show');
            this.$Progress.finish();
        },
        addContact(){
            this.$Progress.start();
            this.editMode = false;
            let details = {'patient': this.user, 'contact': {}};
            Fire.$emit('ContactDataFill', details);
            $('#contactModal').modal('show');
            this.$Progress.finish();
        },
        closeModal(){
            $('#allergyModal').modal('hide');
            $('#contactModal').modal('hide');
            $('#contactModal').modal('hide');
        },
        editAllergy(allergy){
            this.$Progress.start();
            this.editMode = true;
            let details = {'allergy': allergy, 'patient':this.user};
            Fire.$emit('AllergyDataFill', (details));
            $('#allergyModal').modal('show');
            this.$Progress.finish();
        },
        editContact(contact){
            this.$Progress.start();
            this.editMode = true;
            let details = {'patient': this.user, 'contact': contact};
            Fire.$emit('ContactDataFill', details);
            $('#contactModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(){
            axios.get('/api/emr/hims/patients/'+this.$route.params.id).then(response =>{
                this.$Progress.finish();
                this.reloadPatient(response);
                toast.fire({icon: 'success',title: 'Profile loaded successfully',});
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Profile not loaded successfully',});
            });
        },
        reloadPatient(response){
            this.user = response.data.user;
            this.nations = response.data.nations;
            Fire.$emit('ApplicantDataFill', this.user);
            Fire.$emit('NextOfKinFill', this.user.nok); 
        },
    }
}
</script>