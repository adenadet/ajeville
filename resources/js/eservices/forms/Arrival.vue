<template>
<div class="card">
    <form role="form">
        <alert-error :form="ArrivalData"></alert-error> 
        <div class="card-body">
            <div class="form-group">
                <label for="exampleInputPassword1">Appointment Detail</label>
                <div disabled type="text" class="form-control" id="patient" name="patient" :class="{'is-invalid' : ArrivalData.errors.has('appointment_id') }">
                    {{appointment != null && appointment.patient != null ? appointment.patient.first_name+' '+appointment.patient.last_name+' on '+appointment.date+'@ '+appointment.schedule+' | '+appointment.service.name: 'Unknown Applicant'}}
                </div>
                <input type="hidden" name="appointment_id" id="appointment_id" v-model="ArrivalData.appointment_id" />
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Patient</label>
                <div class="form-control" id="patient" name="patient" disabled :class="{'is-invalid' : ArrivalData.errors.has('patient_id') }">
                    {{appointment != null && appointment.patient != null ? appointment.patient.first_name+' '+appointment.patient.middle_name+' '+appointment.patient.last_name : 'Unknown Applicant'}}
                </div>
                <input type="hidden" name="patient_id" id="patient_id" v-model="ArrivalData.patient_id" />
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Services</label>
                <div class="form-control" id="service" name="service" disabled :class="{'is-invalid' : ArrivalData.errors.has('service_id') }">
                    {{appointment != null && appointment.service != null ? appointment.service.name: 'No Service Chosen'}}
                </div>
                <input type="hidden" name="service_id" id="service_id" v-model="ArrivalData.service_id" />
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Unique ID</label>
                <input type="text" class="form-control" id="unique_id" name="unique_id" placeholder="Unique ID"  v-model="ArrivalData.unique_id">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Details</label>
                <textarea rows=5 class="form-control" id="details" name="details" placeholder="Arrival Details"  v-model="ArrivalData.details">
                </textarea>
            </div>
        </div>
        
        <div class="card-footer">
            <button @click.prevent="confirmArrival" type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>
</template>
<script>
export default {
    data(){
        return  {
            appointment_id: 0,
            appointments: {},
            ArrivalData: new Form({
                patient_id:'', 
                service_id:'', 
                appointment_id:'',
                unique_id: '', 
                remarks: ''
            }),
            loading: false,
            paySpecific: false,
        }
    },
    mounted() {},
    emits:['refreshAppointment'],
    methods:{
        confirmArrival(){
            this.loading = true;
            this.ArrivalData.put('/api/emr/appointments/toDoctor/'+this.appointment.id)
            .then(response =>{
                this.loading = false;
                this.$emit('refreshAppointment', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Payment has been made',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
    },
    props:{
        appointment: Object,
    },
    watch:{
        appointment(){
            this.ArrivalData.appointment_id = this.appointment.id;
            this.ArrivalData.patient_id = this.appointment.patient_id; 
            this.ArrivalData.service_id = this.appointment.service_id; 
        }
    }
}
</script>