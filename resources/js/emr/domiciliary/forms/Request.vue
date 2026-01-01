<template>
<form>
    <alert-error :form="requestForm"></alert-error> 
    <div class="row">
        <div class="col-sm-12" v-if="((patient != null) && (patient.first_name != null) && (typeof(patient.first_name) != 'undefined'))">
            <div class="form-group">
                <label>Patient</label>
                <div class="form-control">{{patient | patientName}}</div>
                <input type="hidden" name="patient_id" id="patient_id" v-model="requestForm.patient_id" />
            </div>
        </div>
        <div class="col-sm-12" v-else>
            <div class="form-group">
                <label>Patient *</label>
                <select type="text" required class="form-control" id="patient_id" name="patient_id" v-model="requestForm.patient_id">
                    <option value="">--Select Patient--</option>
                    <option v-for="patient in patients" :value="patient.id" :key="patient.id">{{patient | patientName}} </option>
                </select>
                <has-error :form="requestForm" field="patient_id"></has-error> 
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" v-model="requestForm.start_date" :class="{'is-invalid' : requestForm.errors.has('start_date') }" :min="today"/>
                <has-error :form="requestForm" field="start_date"></has-error> 
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>End Date (leave empty for undefined)*</label>
                <input type="date" class="form-control" id="end_date" name="end_date" v-model="requestForm.end_date" :class="{'is-invalid' : requestForm.errors.has('end_date') }" :min="today"/>
                <has-error :form="requestForm" field="end_date"></has-error> 
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Payment Method</label>
                <select class="form-control" id="payment_type" name="payment_type" v-model="requestForm.payment_type" :class="{'is-invalid' : requestForm.errors.has('payment_type') }">
                    <option value="">--Select Payment--</option>
                    <option value="direct">Direct Payment</option>
                    <option value="insurance">Insurance</option>
                    <option value="company">Company</option>
                    <option value="third party">Third Party</option>
                </select>
                <has-error :form="requestForm" field="payment_type"></has-error> 
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label>HCA Visit Daily</label>
                <input type="number" class="form-control" id="start_date" name="hca_daily" v-model="requestForm.hca_daily" :class="{'is-invalid' : requestForm.errors.has('hca_daily') }"/>
                <has-error :form="requestForm" field="hca_daily"></has-error> 
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>RN Visit Daily</label>
                <input type="number" class="form-control" id="rn_daily" name="rn_daily" v-model="requestForm.rn_daily" :class="{'is-invalid' : requestForm.errors.has('rn_daily') }"/>
                <has-error :form="requestForm" field="rn_daily"></has-error> 
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>BSc Visit Daily</label>
                <input type="number" class="form-control" id="bsc_daily" name="bsc_daily" v-model="requestForm.bsc_daily" :class="{'is-invalid' : requestForm.errors.has('bsc_daily') }"/>
                <has-error :form="requestForm" field="bsc_daily"></has-error> 
            <has-error :form="requestForm" field="bsc_daily"></has-error> 
            </div>
        </div>
    </div>        
    <button @click.prevent="editMode ? updateRequest() : createRequest()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
</form>
</template>
<script>
export default {
    data(){
        return  {
            requestForm: new Form({
                id:"",
                patient_id: "",
                start_date: '',
                end_date: '',
                payment_type: '',
                hca_daily: 0,
                rn_daily: 0,
                bsc_daily: 0,
            }),
            today: '',
        }
    },
    mounted() {
        Fire.$on('requestDataFill', request =>{
            if (request != null){this.requestForm.fill(request);}
            else{this.requestForm.reset();}
        });
    },
    methods:{
        createRequest(){
            this.$Progress.start();
            this.requestForm.post('/api/emr/domiciliary/requests')
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('refreshResponse', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Domiliciary Request has been created',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });  
        },
        updateRequest(){
            this.$Progress.start();
            this.requestForm.put('/api/emr/domiciliary/requests/'+this.requestForm.id)
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('refreshResponse', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Domiciliary Request has been updated',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
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
    props:{
        applicant: Object,
        editMode: Boolean,   
        nations: Array, 
        patient: Object,
        patients: Array,
    }
}
</script>