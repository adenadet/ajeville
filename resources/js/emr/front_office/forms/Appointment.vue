<template>
<section class="overlay-wrapper">
    <form @submit.prevent="editMode ? updateAppointment() : createAppointment()">
    <!--form-->
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Patient</label>
                <select v-model="appointmentData.patient_id" class="form-control" @change="onPatientChange">
                    <option value="">-- Select Patient --</option>
                    <option v-for="p in patients" :key="p.id" :value="p.id">{{ FullName(p.user) }} ({{ p.unique_id }})</option>
                    <option value="new">+ Create New Patient</option>
                </select>
            </div>
        </div>
        <div class="row"  v-if="selectedPatient && selectedPatient.hmo_plan">
            <div class="alert alert-info">
                <strong>Insurance:</strong>{{ selectedPatient.hmo_plan.provider.name }} – {{ selectedPatient.hmo_plan.name }}
            </div>
        </div>
        <div class="row"  v-else>
            <div v-if="appointmentData.patient_id === 'new'" class="border rounded p-3 mb-3">
                <h6 class="mb-3">New Patient Details</h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">First Name</label>
                        <input v-model="newPatient.first_name" class="form-control" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Last Name</label>
                        <input v-model="newPatient.last_name" class="form-control" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input v-model="newPatient.phone" class="form-control" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Gender</label>
                        <select v-model="newPatient.gender" class="form-control">
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
                    <option v-for="b in branches" :key="b.id" :value="b.id">
                    {{ b.name }}
                    </option>
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
    data() {
        return {
            branches: [],
            consultants: [],
            patients: [],
            specialties: [],
            
            filteredConsultants: [],
            loading: false,
            loadingSlots: false,
            selectedPatient: null,
            service_types: [],
            timeSlots: [],
            appointmentData: new Form({
                branch_id: '',
                consultant_id: '',
                date: '',
                patient_id: '',
                patient: {
                    first_name: '',
                    last_name: '',
                    phone: '',
                    gender: '',
                    provider_id: '',
                    plan_id: '',
                },
                plan_id: '',
                remarks: '',
                specialty_id: '',
                time_slot: '',
                type: '',
                service_type_id: '',
            }),

            newPatient: {
                first_name: '',
                last_name: '',
                phone: '',
                gender: '',
                provider_id: '',
                plan_id: '',
            }
        }
    },
    emits:['refreshAppointmentForm'],
    methods: {
        createAppointment(){
            alert("Working");
            this.loading = true;
            this.appointmentData.post('/api/emr/hims/appointments')
            .then(response =>{
                this.$emit('refreshAppointmentForm', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Appointment details has been created',
                    showConfirmButton: false,
                    timer: 1500
                    });
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
                alert("Missing Details");
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
            this.selectedPatient = this.patients.find(p => p.id === this.appointmentData.patient_id) || null
        },
        submit() {
            console.log('Submitting', this.form, this.newPatient)
        },
        updateAppointment(){
            alert("Working 2");
            this.loading = true;
            this.appointmentData.put('/api/emr/hims/appointments/'+this.appointment.id)
            .then(response =>{
                this.$emit('refreshAppointmentForm', response);
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
            this.loading = false;
        }
    }
}
</script>
