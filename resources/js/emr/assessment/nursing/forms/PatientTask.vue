<template>
    <section>
        <form @submit.prevent="editMode ? editPatientTask() : createPatientTask()">
            <alert-error :form="patientTaskForm"></alert-error>
            <div class="row" v-if="((patient != null) && (patient.first_name != null) && (typeof (patient.first_name) != 'undefined'))">
                <div class="col-sm-9">
                    <div class="form-group">
                        <label>Patient</label>
                        <div class="form-control">{{patient | patientName}}</div>
                        <input type="hidden" name="patient_id" id="patient_id" v-model="patientTaskForm.patient_id" />
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Domiciliary Service</label>
                        <select class="form-control" id="domiciliary" name="domiciliary" v-model="patientTaskForm.domiciliary"
                            :class="{ 'is-invalid': patientTaskForm.errors.has('domiciliary') }">
                            <option value=''>--Select If Domiciliary--</option>
                            <option value='1'>Yes</option>
                            <option value='0'>No</option>
                        </select>
                        <has-error :form="patientTaskForm" field="domiciliary"></has-error>
                    </div>
                </div> 
            </div>
            <div class="row" v-else>
                <div class="col-sm-9">
                    <div class="form-group">
                        <label>Patient *</label>
                        <select type="text" required class="form-control" id="patient_id" name="patient_id"
                            v-model="patientTaskForm.patient_id">
                            <option value="">--Select Patient--</option>
                            <option v-for="patient in patients" :value="patient.id" :key="patient.id">
                                {{ patient.last_name + ', ' + patient.first_name + ' ' + patient.middle_name }} </option>
                        </select>
                        <has-error :form="patientTaskForm" field="patient_id"></has-error>
                    </div>
                </div>
                <div class="col-sm-3" v-if="!(isNaN(domiciliary))">
                    <div class="form-group">
                        <label>Domiciliary Service</label>
                        <input type="text" class="form-control" :value="this.patientTaskForm.domiciliary == 1 ? 'Yes' : 'No'" :class="{ 'is-invalid': patientTaskForm.errors.has('domiciliary') }"/>
                        <input type="hidden"  id="domiciliary" name="domiciliary" :form="patientTaskForm"  v-model="patientTaskForm.domiciliary" />
                    </div>
                </div> 
                <div class="col-sm-3" v-else>
                    <div class="form-group">
                        <label>Domiciliary Service</label>
                        <select class="form-control" id="domiciliary" name="domiciliary"
                            :class="{ 'is-invalid': patientTaskForm.errors.has('domiciliary') }">
                            <option value=''>--Select If Domiciliary--</option>
                            <option value='1'>Yes</option>
                            <option value='0'>No</option>
                        </select>
                        <has-error :form="patientTaskForm" field="domiciliary"></has-error>
                    </div>
                </div>  
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Task</label>
                        <select class="form-control" id="task_id" name="task_id" v-model="patientTaskForm.task_id"
                            :class="{ 'is-invalid': patientTaskForm.errors.has('task_id') }">
                            <option value=''>--Select Task--</option>
                            <option v-for="task in tasks" :value='task.id'>{{ task.name }}</option>
                        </select>
                        <has-error :form="patientTaskForm" field="task_id"></has-error>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" v-model="patientTaskForm.start_date" :class="{ 'is-invalid': patientTaskForm.errors.has('start_date') }" :min="today" />
                        <has-error :form="patientTaskForm" field="start_date"></has-error>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Repeating </label>
                        <select class="form-control" id="repeating" name="repeating" v-model="patientTaskForm.repeating"
                            :class="{ 'is-invalid': patientTaskForm.errors.has('repeating') }">
                            <option value=''>--Select If Repeating</option>
                            <option value='1'>Yes</option>
                            <option value='0'>No</option>
                        </select>
                        <has-error :form="patientTaskForm" field="repeating"></has-error>
                    </div>
                </div>
                <div class="col-sm-4" v-show="patientTaskForm.repeating == 1">
                    <div class="form-group">
                        <label>Frequency</label>
                        <select class="form-control" id="frequency_id" name="frequency_id" v-model="patientTaskForm.frequency_id"
                            :class="{ 'is-invalid': patientTaskForm.errors.has('frequency_id') }">
                            <option value=''>--Select Frequency--</option>
                            <option v-for="frequency in frequencies" :value='frequency.id'>{{ frequency.name }}</option>
                        </select>
                        <has-error :form="patientTaskForm" field="frequency_id"></has-error>
                    </div>
                </div>
                <div class="col-sm-4" v-show="patientTaskForm.repeating == 1">
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" v-model="patientTaskForm.quantity" :class="{ 'is-invalid': patientTaskForm.errors.has('quantity') }" :min="today" />
                        <has-error :form="patientTaskForm" field="quantity"></has-error>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Details</label>
                        <wysiwyg id="details" name="details" v-model="patientTaskForm.details" :class="{ 'is-invalid': patientTaskForm.errors.has('details') }"/>
                        <has-error :form="patientTaskForm" field="details"></has-error>
                    </div>
                </div>
            </div>
            <button class="btn btn-sm btn-primary" type="submit">Submit</button>
        </form>
    </section>
</template>
<script>
export default {
    data() {
        return {
            patientTaskForm: new Form({
                patient_id: '',
                task_id: '',
                id: '',
                repeating: '',
                start_date: '',
                frequency_id: '',
                quantity: 0,
                details: '',
                domiciliary: '',
            }),
            today: '',
            patients: [],
            tasks: [],
            frequencies: [],
        }
    },
    mounted() {
        this.getInitials();
        const date = new Date().toJSON().slice(0, 10);
        this.today = date;
        Fire.$on('patientTaskDataFill', patient_task => {
            this.patientTaskForm.fill(patient_task);
            if(this.patient != null){this.patientTaskForm.patient_id = this.patient.id;}
        });
        Fire.$on('AfterCreation', () => {
            //axios.get("api/profile").then(({ data }) => (this.ApplicantData.fill(data)));
        });
    },
    methods: {
        createPatientTask() {
            this.$Progress.start();
            this.patientTaskForm.post('/api/emr/nursing/patient_tasks')
            .then(response => {
                this.$Progress.finish();
                Fire.$emit('refreshPatientTasks', response.data.patient);
                Swal.fire({
                    icon: 'success',
                    title: 'The Patient Task details has been created',
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
        editPatientTask() {
            console.log("Tested");
            this.$Progress.start();
            this.patientTaskForm.put('/api/emr/nursing/patient_tasks/'+this.patientTaskForm.id)
            .then(response => {
                this.$Progress.finish();
                Fire.$emit('refreshPatientTasks');
                Swal.fire({
                    icon: 'success',
                    title: 'The Patient Task has been updated',
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
        getInitials() {
            axios.get('/api/emr/nursing/tasks/initials')
            .then(response => {
                this.refresh(response);
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Nursing Tasks were loaded successfully',
                });
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Nursing Tasks were not loaded successfully',
                })
            });
        },
        refresh(response) {
            this.patients = response.data.patients;
            this.tasks = response.data.tasks;
            this.frequencies = response.data.frequencies;
        },
    },
    props: {
        editMode: Boolean,
        patient: Object,
        domiciliary: Number,
    }
}
</script>