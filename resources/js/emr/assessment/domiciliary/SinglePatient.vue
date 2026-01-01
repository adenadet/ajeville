<template>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-md-5 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">Patient Detail</div>
                <div class="card-body">
                    <div class="text-center user-info">
                        <img class="img-fluid" :src="(patient.image) ? '/img/profile/'+patient.image : ''" width="300" height="auto" alt="avatar">
                        <p class=""></p>
                    </div>
                    <div class="user-info-list">
                        <div class="">
                            <ul class="contacts-block list-unstyled">
                                <li class="contacts-block__item">
                                    <i class="fa fa-user mr-1" width="24" height="24"></i> {{patient.first_name}} {{patient.middle_name}} {{patient.last_name}}
                                </li>
                                <li class="contacts-block__item">
                                    <i class="fa fa-calendar mr-1" width="24" height="24"></i> {{patient.dob | ExcelDate}}
                                </li>
                                <li class="contacts-block__item">
                                    <i class="fa fa-map-marker mr-1" width="24" height="24"></i>{{patient.uk_address}}</li>
                                <li class="contacts-block__item">
                                    <a :href="'mailto:'+patient.email"><i class="fa fa-envelope mr-1" width="24" height="24"></i> {{patient.email}}</a>
                                </li>
                                <li class="contacts-block__item">
                                    <i class="fa fa-phone mr-1" width="24" height="24"></i> {{patient.phone}} {{patient.alt_phone ? ', '+patient.alt_phone: ''}} 
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
                        <li class="nav-item"><a class="nav-link active" href="#allergies" data-toggle="tab">Allergies</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contacts" data-toggle="tab">Contacts</a></li>
                        <li class="nav-item"><a class="nav-link" href="#bio-data" data-toggle="tab">Bio Data</a></li>
                        <li class="nav-item"><a class="nav-link" href="#next-of-kin" data-toggle="tab">Next of Kin</a></li>
                        <li class="nav-item"><a class="nav-link" href="#prescriptions" data-toggle="tab">Prescriptions</a></li>
                        <li class="nav-item"><a class="nav-link" href="#administrations" data-toggle="tab">Drug Admin</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tasks" data-toggle="tab">Tasks</a></li>
                        <li class="nav-item"><a class="nav-link" href="#plans" data-toggle="tab">Plans</a></li>
                        <li class="nav-item"><a class="nav-link" href="#fluid" data-toggle="tab">Fluid Chart</a></li>
                    </ul>
                </div>
                <div class="col-md-10">
                    <div class="tab-content">
                        <div class="tab-pane active" id="allergies">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">List of Allergies</h3>
                                    <div class="card-tools"><button type="submit" class="btn btn-tool"><i class="fas fa-plus"></i></button></div>
                                </div>
                                <div class="row m-0 p-3">
                                    <div class="col-sm-4">
                                        <div class="position-relative p-3 bg-pink" v-for="allergy in patient.allergies" :key="allergy.id">
                                            <div class="ribbon-wrapper">
                                                <div class="ribbon bg-maroon disabled">
                                                Allergy
                                                </div>
                                            </div>
                                            <p v-html="allergy.allergy"></p>
                                            <small v-html="allergy.description"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="contacts">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">List of Contacts</h3>
                                    <div class="card-tools"><button type="submit" class="btn btn-default"><i class="fas fa-plus"></i></button></div>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-striped table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Name</th>
                                            <th>Address</th>
                                            <th>Phone Number</th>
                                            <th>Email</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(contact, index) in patient.contacts" :key="contact.id" >
                                            <td>{{index | addOne}}</td>
                                            <td>{{contact.name}}</td>
                                            <td v-html="contact.address"></td>
                                            <td>{{contact.phone+(contact.alt_phone != null ? ', '+contact.alt_phone : '')}}</td>
                                            <td>{{contact.email_address}}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></button>
                                                    <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="prescriptions">
                            <PatientChartPrescription />
                        </div>
                        <div class="tab-pane" id="administrations">
                            <PatientChartAdmin />
                        </div>
                        <div class="tab-pane" id="tasks">
                            <PatientChartTask />
                        </div>
                        <div class="tab-pane" id="plans">
                            <PatientChartPlan />
                        </div>
                        <div class="tab-pane" id="fluid">
                            <PatientChartFluid />
                        </div>
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
            patient: {},
            areas:[],  
            branches:[],  
            departments:[], 
            editMode: true, 
            nok:{},
            states:[],
            nations: [],  
            user:{}, 
        }
    },
    created() {
        this.getInitials();
        Fire.$on('Reload', response =>{this.refreshProfile(response);});
    },
    methods:{
        getInitials(){
            axios.get('/api/emr/domiliciaries/'+this.$route.params.id)
            .then(response =>{
                this.$Progress.finish();
                this.reloadPatient(response);
                toast.fire({
                    icon: 'success',
                    title: 'Profile loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Profile not loaded successfully',
                })
            });
        },
        getProfilePic(){
            let  photo = (this.form.image.length >= 150) ? this.form.image : "./"+this.form.image;
            return photo;
        },
        reloadPatient(response){
            this.user = response.data.user;
            this.areas = response.data.areas;
            this.states = response.data.states;
            this.nok = response.data.nok;
            this.nations = response.data.nations;
            this.patient = response.data.patient;
            Fire.$emit('BioDataFill', this.user);
            Fire.$emit('NextOfKinFill', this.nok); 
        },
        updateProfilePic(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['size'] < 2000000){
                reader.onloadend = (e) => {
                    this.form.image = reader.result
                    }
                reader.readAsDataURL(file)
            }
            else{
                swal.fire({
                    type: 'error',
                    title: 'File is too large'
                });
            }
        }
    }
}
</script>