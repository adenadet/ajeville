<template>
    <section>
        <div class="modal fade" id="bioDataModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header"><h4 class="modal-title" v-html="editMode ? 'Edit User' : 'Create User'"></h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button></div>
                    <div class="modal-body"><HrUserFormBioData :editMode="editMode"/></div>
                </div>
            </div>
        </div>
        <div class="card-header bg-dark">
            <h4 class="card-title">Bio Data</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>First Name *</label>
                        <div class="form-control" v-html="patient.user.first_name" ></div> 
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Middle Name</label>
                        <div class="form-control" v-html="patient.user.middle_name"></div> 
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Last Name*</label>
                        <div class="form-control" v-html="patient.user.last_name"></div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Address*</label>
                        <div class="form-control" v-html="patient.user.street"></div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Address2</label>
                        <div class="form-control" v-html="patient.user.street2"></div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>City*</label>
                        <div class="form-control" v-html="patient.user.city"></div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label>State</label>
                        <div class="form-control" v-html="patient.user.state_id"></div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label>Area</label>
                        <div class="form-control" v-html="patient.user.area_id"></div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <div class="form-control"  v-html="patient.user.phone"></div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Alternate Phone</label>
                        <div class="form-control" v-html="patient.user.alt_phone"></div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Email Address</label>
                        <div class="form-control"  v-html="patient.user.email"></div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Sex</label>
                        <div class="form-control" v-html="patient.user.sex"></div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <label>Date of Birth</label>
                    <div class="form-group">
                        <div class="form-control" v-html="patient.user.dob"></div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Unique ID</label>
                        <div class="form-control" v-html="patient.user.unique_id"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button @click="editUser(patient.user)" class="btn btn-sm bg-dark float-right">Edit</button>
        </div>
    </section>
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
            editMode: false,
            insurance: {},
            insurances: [], 
        }
    },
    mounted() {
        /*Fire.$on('patientReset', () => {
            this.getInitials(this.patient.id);
        });*/
    },
    methods:{
        closeModal(){
            $('#bioDataModal').modal('hide');
        },
        editUser(user){
            this.$Progress.start();
            this.editMode = true;
            //Fire.$emit('BioDataFill', user);
            $('#bioDataModal').modal('show');
            this.$Progress.finish();
        },
    },
    props:{
        
    },
    watch:{

    }
}
</script>