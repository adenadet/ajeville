<template>
<section class="row clearfix">
    <div class="col-md-12">
        <form @submit.prevent="editMode ? updateAppointment() : createAppointment() ">
            <alert-error :form="appointmentData"></alert-error> 
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="form-group" v-if="!editMode">
                        <label>Applicant</label>
                        <model-list-select class="form-control" :list="patients" v-model="appointmentData.patient_id" option-value="id" :custom-text="codeAndNameAndDesc" placeholder="Select Applicant" />
                    </div>
                    <div class="form-group" v-if="editMode">
                        <label>Applicant</label>
                        <div class="form-control">{{ FullName(appointment.patient)  }}</div>
                        <input type="hidden" name="patient_id" v-model="appointmentData.patient_id" />
                    </div>
                </div>
                <div class="col-md-12 col-sm-12">
                    <div class="form-group" v-if="!editMode">
                        <label>Service</label>
                        <select class="form-control" id="service_id" name="service_id" v-model="appointmentData.service_id" required>
                            <option value="">--Select Service--</option>
                            <option v-for="service in services" :key="service.id" :value="service.id">{{service.name}}</option>
                        </select>
                    </div>
                    <div class="form-group" v-if="editMode">
                        <label>Service</label>
                        <div class="form-control">{{ appointment.service != null ? appointment.service.name : 'Awaiting'  }}</div>
                        <input type="hidden" name="service_id" v-model="appointmentData.service_id" />
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label>Date</label>
                        <input class="form-control" type="date" name="date" id="date" v-model="appointmentData.date" @change="searchSchedule()"/>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group" v-if="editMode">
                        <label>Schedule</label>
                        <input type="time" class="form-control" id="schedule" name="schedule" v-model="appointmentData.schedule" required/>
                    </div>
                    <div class="form-group" v-else>
                        <label>Schedule</label>
                        <select class="form-control" id="schedule" name="schedule" v-model="appointmentData.schedule" required>
                            <option value=''>--Select Available Time--</option>
                            <option v-for="(schedule, index) in schedules" :key="index" :value="schedule.schedule">{{schedule.schedule}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <input type="submit" name="submit" class="submit btn btn-success" value="Submit" />
        </form>
    </div>
</section>
</template>
<script>
import { ModelListSelect } from 'vue-search-select';
export default {
    components: {ModelListSelect},
    data(){
        return  {
            appointmentData: new Form({
                patient_id: "",
                schedule: "",
                service_id: "",
                date: "",
                id: "",
            }),
            loading: false,
            schedules: [],
            services: [],
            patients: []                                                                                                                                                                                                                                                     ,
        }
    },
    emits:['refreshAppointment'],
    mounted() {this.getAllInitials();},
    methods:{
        codeAndNameAndDesc(item){
            return `${item.last_name}, ${item.first_name} ${item.middle_name != null ? item.middle_name : '' }`;
        },
        createAppointment(){
            this.loading = true;
            this.appointmentData.post('/api/emr/appointments')
            .then(response => {
                this.loading = false;
                this.$swal.fire({
                    icon: 'success',
                    title: 'Successful',
                    text: 'A new appointment was created successfully',
                });
                this.$emit('refreshAppointment', response);
            })
            .close(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });   
        },
        getAllInitials(){
            this.loading= true;
            axios.get('/api/emr/appointments/initials')
            .then(response =>{
                this.services = response.data.services;
                this.patients = response.data.patients;
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Appointment form not loaded successfully',
                })
            });
        },
        updateAppointment(){
            this.loading = true;
            this.appointmentData.put('/api/emr/appointments/'+this.appointmentData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('refreshAppointment', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Appointment details has been modified',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            });          
        },
        searchSchedule(){
            if (this.appointmentData.service_id == ""){
                alert("Please select the service type");
                this.appointmentData.date = "";
                return;
            }
            axios.get('/api/schedules?service_id='+this.appointmentData.service_id+'&date='+this.appointmentData.date)
            .then(response =>{
                this.schedules = response.data.schedules;
                this.loading = false
            })
            .catch(()=>{
                this.loading = false
                toast.fire({
                    icon: 'error',
                    title: 'Schedules not loaded successfully',
                })
            });
        },
    },
    props:{
        editMode: Boolean,
        appointment: Object,
    },
    watch:{
        appointment(){
            this.appointmentData.fill(this.appointment);
        }
    }
}
</script>