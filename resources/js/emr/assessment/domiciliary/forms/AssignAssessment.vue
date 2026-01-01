<template>
    <form>
        <alert-error :form="assessRequestForm"></alert-error>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Patient for Accessment</label>
                    <input disabled type="text" class="form-control"
                        :value="patient != null ? patient.last_name + ', ' + patient.first_name + ' ' + patient.middle_name : 'Loading Patient Data'" />
                    <input type="hidden" name="patient_id" id="patient_id" v-model="assessRequestForm.patient_id" />
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Staff *</label>
                    <select type="text" required class="form-control" id="staff_id" name="staff_id"
                        v-model="assessRequestForm.staff_id">
                        <option value="">--Select Staff--</option>
                        <option v-for="staff in staffs" :value="staff.id">{{ staff.unique_id }} | {{ staff.user != null ?
                                staff.user.first_name + ' ' + staff.user.last_name : 'Old Staff'
                        }}</option>
                    </select>
                    <has-error :form="assessRequestForm" field="staff_id"></has-error>
                </div>
            </div>
        </div>
        <button @click.prevent="assignRequest()" type="submit" name="submit"
            class="submit btn btn-success">Submit</button>
    </form>
</template>
<script>
export default {
    data() {
        return {
            assessRequestForm: new Form({
                patient_id: '',
                domiciliary_id: "",
                staff_id: "",
            }),
        }
    },
    mounted() {
        Fire.$on('assessRequestDataFill', request => {
            if (request != null) {
                this.assessRequestForm.patient_id = request.patient.id
                this.assessRequestForm.domiciliary_id = request.id;
                this.assessRequestForm.staff_id = request.assessed_by;
            }
            else { this.assessRequestForm.reset(); }
        });
    },
    methods: {
        assignRequest() {
            this.$Progress.start();
            this.assessRequestForm.put('/api/emr/domiciliary/requests/assign/' + this.assessRequestForm.domiciliary_id)
                .then(response => {
                    this.$Progress.finish();
                    Fire.$emit('refreshResponse', response);
                    Swal.fire({
                        icon: 'success',
                        title: 'The Domiliciary Request has been updated',
                        showConfirmButton: false,
                        timer: 1500
                    });
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
    },
    props: {
        applicant: Object,
        editMode: Boolean,
        nations: Array,
        patient: Object,
        patients: Array,
        staffs: Array,
    }
}
</script>