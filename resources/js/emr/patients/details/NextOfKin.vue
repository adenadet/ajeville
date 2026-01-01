<template>
    <section class="card">
        <!--div class="modal fade" id="nextOfKinDataModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header"><h4 class="modal-title" v-html="editMode ? 'Edit User' : 'Create User'"></h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button></div>
                    <div class="modal-body"><HrUserFormNOK :editMode="editMode"/></div>
                </div>
            </div>
        </div-->
        <div class="card-header bg-dark">
            <h4 class="card-title">Next of Kin</h4>
        </div>
        <div class="card-body" v-if="patient.user.next_of_kin != null">
            <div class="row">
                <div class="col-sm-9">
                    <div class="form-group">
                        <label>Name * </label>
                        <div class="form-control" v-html="patient.user.next_of_kin.name"></div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Relationship *</label>
                        <div class="form-control" v-html="patient.user.next_of_kin.relationship"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="form-group">
                        <label>Address</label>
                        <div class="form-control" v-html="patient.user.next_of_kin.address"></div>
                    </div>
                </div>
            </div>    
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Phone Number*</label>
                        <div class="form-control" v-html="patient.user.next_of_kin.phone"></div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Email</label>
                        <div class="form-control" v-html="patient.user.next_of_kin.email"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body" v-else>
            No Next of Kin has been added
        </div>
        <div class="card-footer">
            <button class="btn btn-sm bg-dark float-right"><i class="fa fa-edit mr-1"></i>Edit</button>
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
            $('#nextOfKinModal').modal('hide');
        },
        editNok(nok){
            this.$Progress.start();
            this.editMode = true;
            //Fire.$emit('NextOfKinFill', {'nok': nok, 'user_id': this.patient.user.id});
            $('#nextOfKinModal').modal('show');
            this.$Progress.finish();
        },
    },
}
</script>