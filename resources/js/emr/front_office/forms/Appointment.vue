<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateAppointment() : createAppointment()">
    <!--form-->
        <div class="row">
            <div class="col-md-3 mb-3">
                {{ editMode ? 'Welcome' : 'Not Working' }}
                <label class="form-label">Patient</label>
                <select v-model.number="appointmentData.patient_type_id" class="form-control">
                    <option value="1">Existing</option>
                    <option value="0">Create New Patient</option>
                </select>
            </div>
            <div class="col-md-9 mb-3" v-if="appointmentData.patient_type_id == '1'">
                <div class="form-group">
                    <label>Select Patient</label>
                    <model-list-select class="form-control" :list="patients" v-model="appointmentData.patient_id" option-value="id" :custom-text="codeAndNameAndDesc" />
                </div>
            </div>
            <div class="col-md-9 mb-3" v-else>
                <div class="row" v-if="appointmentData.patient_type_id == '0'">
                    <div class="col-md-3 mb-3 form-group">
                        <label class="form-label">First Name</label>
                        <input v-model="appointmentData.patient.first_name" class="form-control" />
                    </div>
                    <div class="col-md-3 mb-3 form-group">
                        <label class="form-label">Last Name</label>
                        <input v-model="appointmentData.patient.last_name" class="form-control" />
                    </div>
                    <div class="col-md-3 mb-3 form-group">
                        <label class="form-label">Phone</label>
                        <input v-model="appointmentData.patient.phone" class="form-control" />
                    </div>
                    <div class="col-md-3 mb-3 form-group">
                        <label class="form-label">Sex</label>
                        <select v-model="appointmentData.patient.sex" class="form-control">
                            <option value="">-- Select --</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>   
            </div>
        </div>
        <div class="row">
            <!-- Branch -->
            <div class="col-md-4 mb-3">
                <label class="form-label">Branch</label>
                <select v-model="appointmentData.branch_id" class="form-control" @change="maybeFetchSlots">
                    <option value="">-- Select Branch --</option>
                    <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                </select>
            </div>

            <!-- Appointment Date -->
            <div class="col-md-4 mb-3">
                <label class="form-label">Appointment Date</label>
                <input type="date" v-model="appointmentData.date" class="form-control"  @change="maybeFetchSlots"/>
            </div>

            <!-- Appointment Type -->
            <div class="col-md-4 mb-3">
                <label class="form-label">Appointment Type</label>
                <select v-model="appointmentData.service_type_id" class="form-control">
                    <option value="">-- Select Service Type --</option>
                    <option v-for="service_type in service_types" :value="service_type.id">{{ service_type.name }}</option>
                </select>
            </div>
        </div>
        <div class="row" v-if="appointmentData.service_type_id == 4">
            <div class="col-md-4 mb-3">
                <label class="form-label">Specialty</label>
                <select v-model="appointmentData.specialty_id" class="form-control" @change="filterConsultants">
                <option value="">-- Select Specialty --</option>
                <option v-for="s in specialties" :key="s.id" :value="s.id">
                    {{ s.name }}
                </option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Consultant</label>
                <select v-model="appointmentData.consultant_id" class="form-control" @change="fetchAvailableSlots">
                    <option value="">-- Select Consultant --</option>
                    <option v-for="c in filteredConsultants" :key="c.id" :value="c.id">
                        {{ FullName(c) }}
                    </option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Preferred Time Slot </label>
                <select v-model="appointmentData.time_slot" class="form-control" :disabled="loadingSlots">
                    <option value="">-- Select Time --</option>
                    <option :value="appointment.time_slot" disabled>{{ appointment.time_slot }}</option>
                    <option v-for="slot in timeSlots" :key="slot.start" :value="slot.start" :disabled="slot.booked">{{ slot.start }} - {{ slot.end }} {{ slot.booked ? '(Booked)' : '' }}</option>
                </select>
                <small v-if="loadingSlots" class="text-muted">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i> Loading available slots...
                </small>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label">Remarks</label>
                    <QuillEditor class="form-control" id="remarks" name="remarks" theme="snow" content-type="html" v-model:content="appointment.remarks"></QuillEditor>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">{{editMode ? 'Update' : 'Create'}} Appointment</button>
        </form>
</section>
</template>
<script>
export default {
    computed: {
        isNewPatient() {
            return this.appointmentData.patient_type_id === 0
        },
        isCashPayment() {
            return this.appointmentData.patient.provider_type_id === 0
        },
        filtered_providers() {
            if (this.isCashPayment) return []
            return this.providers.filter(
                p => p.hmo_type_id === this.appointmentData.patient.provider_type_id
            )
        },
        filtered_plans() {
            if (!this.appointmentData.patient.insurance.provider_id) return []
            return this.plans.filter(
                pl => pl.provider_id === this.appointmentData.patient.insurance.provider_id
            )
        }
    },
    data() {
        return {
            appointmentData: new Form({
                patient_type_id: 1, // 1 = existing, 0 = new
                patient_id: '',
                branch_id: '',
                consultant_id: '',
                date: '',
                patient: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    sex: '',
                    provider_type_id: 0, // default CASH
                    insurance: {
                        provider_id: '',
                        plan_id: '',
                    },
                },
                remarks: '',
                specialty_id: '',
                time_slot: '',
                service_type_id: '',
            }),
            branches: [],
            consultants: [],
            filteredConsultants: [],
            loading: false,
            loadingSlots: false,
            patients: [],
            payment_methods: [],
            plans: [],
            providers: [],
            payment_methods: [],
            selectedPatient: null,
            service_types: [],
            specialties: [],
            timeSlots: [],
            
        }
    },
    emits:['refreshAppointmentForm'],
    methods: {
        codeAndNameAndDesc (item) {
            return `${item.user.last_name}, ${item.user.first_name} ${item.user.middle_name} (${item.unique_id})`
        },
        createAppointment(){
            this.loading = true;
            this.appointmentData.post('/api/emr/hims/appointments')
            .then(response =>{
                this.$emit('refreshAppointmentForm');
                this.$swal.fire({icon: 'success', title: 'The Appointment details has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            })
            .finally(() => {this.loading = false;});
        },
        fetchAvailableSlots() {
            if (
                !this.appointmentData.branch_id || !this.appointmentData.consultant_id || !this.appointmentData.date
            ) {
                this.timeSlots = []
                return
            }
            this.loadingSlots = true

            axios.post('/api/emr/hims/appointments/available_slots', {
                branch_id: this.appointmentData.branch_id,
                consultant_id: this.appointmentData.consultant_id,
                date: this.appointmentData.date
            })
            .then(res => {
                this.timeSlots = res.data
            })
            .finally(() => {
                this.loadingSlots = false
            })
        },
        filterConsultants() {
            this.filteredConsultants = []

            if (this.appointmentData.specialty_id == '') {
                this.filteredConsultants = this.consultants;
            }
            else{
                const specialty = this.specialties.find(s => s.id === this.appointmentData.specialty_id)
                if (!specialty || !specialty.doctors) {this.filteredConsultants = []; return;}
                // Extract ONLY the user objects
                this.filteredConsultants = specialty.doctors.filter(d => d.user).map(d => d.user)        // safety check
            }
        },
        getAllInitials(){
            this.loading = true
            axios.get('/api/emr/hims/appointments/initials')
            .then(res => {
                this.branches = res.data.branches;
                this.consultants = res.data.consultants;
                this.filteredConsultants = res.data.consultants;
                this.patients = res.data.patients;
                this.providers = res.data.providers;
                this.provider_types = res.data.provider_types;
                this.plans = res.data.plans;
                this.service_types = res.data.service_types;
                this.specialties = res.data.specialties;
            })
            .finally(() => {
                this.loading = false
            })
        },
        maybeFetchSlots() {
            if (this.appointmentData.consultant_id) {
                this.fetchAvailableSlots()
            }
        },
        onPatientChange() {
            if (this.appointmentData.patient_id === 0) {
                // New patient
                this.selectedPatient = null
                this.appointmentData.patient = {
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    sex: '',
                    provider_type_id: '',
                    insurance:{
                        provider_id: '',
                        plan_id: '',
                    }
                }
            } 
            else {
                // Existing patient
                this.selectedPatient = this.patients.find(
                    p => p.id === this.appointmentData.patient_id
                )

                // Force CASH for existing patients
                this.appointmentData.patient.provider_type_id = 0
                this.appointmentData.patient.provider_id = ''
                this.appointmentData.patient.plan_id = ''
            }
        },
        submit() {
            console.log('Submitting', this.form, this.newPatient)
        },
        updateAppointment(){
            this.loading = true;
            this.appointmentData.put('/api/emr/hims/appointments/'+this.appointment.id)
            .then(response =>{
                this.$emit('refreshAppointmentForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Appointment details has been updated',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            })
            .finally(() => {this.loading = false;});
        },
    },
    mounted() {
        this.getAllInitials();
    },
    props:{
        appointment: Object,
        editMode: Boolean,
    },
    watch:{
        appointment(){
            this.loading = true;
            this.appointmentData.fill(this.appointment);
            if (this.appointment.patient != null ){
                this.appointmentData.patient_type_id = 1;
            }
            this.appointmentData.patient = this.appointment != null && !this.editMode ? this.appointment.patient :  {first_name: '', last_name: '', email: '', phone: '', sex: '', provider_type_id: '', insurance: { provider_id: '', plan_id: '',}},
            this.loading = false;
        },
        'appointmentData.patient_type_id'(val) {
            if (val === 0) {
                // New patient
                this.appointmentData.patient_id = ''
                this.appointmentData.patient = {
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    sex: '',
                    provider_type_id: 0,
                    insurance: {
                        provider_id: '',
                        plan_id: '',
                    },
                }
            } else {
                // Existing patient
                this.appointmentData.patient = {
                    provider_type_id: 0,
                    insurance: {
                        provider_id: '',
                        plan_id: '',
                    }
                }
            }
        }
    }
}
</script>
