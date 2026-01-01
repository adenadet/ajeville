<template>
<div class="overlay-wrapper p-0">
    <div class="modal fade" id="BioDataModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{editMode ? 'Update ': 'Create New '}} User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <UmsFormBioData :user.sync="user" :editMode.sync="editMode" @Reload="closeModal"/>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>First Name *</label>
                    <div class="form-control" id="first_name" name="first_name" placeholder="First Name *">{{ user.first_name }}</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Middle Name</label>
                    <div class="form-control" id="middle_name" name="middle_name" placeholder="middle Name" v-html="user.middle_name"></div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Last Name*</label>
                    <div class="form-control" id="last_name" name="last_name" placeholder="Last Name *" required v-html="user.last_name"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Address*</label>
                    <div class="form-control" v-html="user.street"></div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label>Address2</label>
                    <div class="form-control" v-html="user.street2"></div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <label>City*</label>
                    <div class="form-control" v-html="user.city"></div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <label>State</label>
                    <div class="form-control" v-html="user.state != null ? user.state.name : ''"></div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <label>LGA</label>
                    <div class="form-control" v-html="user.area != null ?user.area.name : ''"></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="form-group">
                    <label>Phone Number</label>
                    <div class="form-control" v-html="user.phone"></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="form-group">
                    <label>Personal Email Address</label>
                    <div class="form-control" v-html="user.personal_email"></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="form-group">
                    <label>Sex</label>
                    <div class="form-control" v-html="user.sex"></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <label>Date of Birth</label>
                <div class="form-group">
                    <div class="form-control" placeholder="Birth Date" v-html="user.dob"></div>
                </div>
            </div>
        </div>
        <button @click="editUser()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </div>
</div>
</template>
<script>
export default {
    data(){
        return  {
            editMode: true,
            editting: true,
            loading: false, 
        }
    },
    emits:['BioDataReload'],
    methods:{
        closeModal(response){
            this.$emit('BioDataReload', response);
            $('#BioDataModal').modal('show');
        },
        editUser(){
            this.loading = true;
            this.editMode = true;
            $('#BioDataModal').modal('show');
            this.loading = false;
        },
    },
    props:{
        user: Object,
    },
    watch:{}
}
</script>
