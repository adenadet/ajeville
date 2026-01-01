<template>
<section>
    <form>
        <alert-error :form="batchForm"></alert-error> 
        <div class="row">
            <div class="col-sm-12" v-if="((patient != null) && (patient.first_name != null) && (typeof(patient.first_name) != 'undefined'))">
                <div class="form-group">
                    <label>Patient*</label>
                    <div class="form-control">{{ patient | patientName }}</div>
                    <input type="hidden" name="patient_id" id="patient_id" v-model="batchForm.patient_id" />
                </div>
            </div>
            <div class="col-sm-12" v-else>
                <div class="form-group">
                    <label>Select Patient*</label>
                    <select type="text" required class="form-control" id="patient_id" name="patient_id" v-model="batchForm.patient_id" @change="updatePatientTask()">
                        <option value="">--Select Patient--</option>
                        <option v-for="patient in patients" :value="patient.id" :key="patient.id">{{patient.last_name+', '+patient.first_name+' '+patient.middle_name}} </option>
                    </select>
                    <has-error :form="batchForm" field="patient_id"></has-error> 
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group"><label>Tasks</label></div>
            </div>
            <div class="col-sm-3" v-for="patient_task in patient_tasks" :key="patient_task.id">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="tasks[]" id="tasks[]" v-model="batchForm.tasks" :value="patient_task.id" :checked="batchForm.tasks.includes(patient_task.id)">
                    <label class="form-check-label">{{patient_task.task.name}}</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" v-model="batchForm.start_date" :class="{'is-invalid' : batchForm.errors.has('start_date') }" :min="today"/>
                    <has-error :form="batchForm" field="start_date"></has-error> 
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" v-model="batchForm.end_date" :class="{'is-invalid' : batchForm.errors.has('end_date') }" :min="today"/>
                    <has-error :form="batchForm" field="end_date"></has-error> 
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Shift Type</label>
                    <select class="form-control" id="shift_type_id" name="shift_type_id" v-model="batchForm.shift_type_id" :class="{'is-invalid' : batchForm.errors.has('shift_type_id') }">
                        <option value="">--Select Shift Type--</option>
                        <option v-for="shift_type in shift_types" :value="shift_type.id">{{shift_type.name}}</option>
                    </select>
                    <has-error :form="batchForm" field="shift_type_id"></has-error> 
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Staff Type</label>
                    <select class="form-control" id="staff_type_id" name="staff_type_id" v-model="batchForm.staff_type_id" :class="{'is-invalid' : batchForm.errors.has('staff_type_id') }">
                        <option value=''>--Select Staff Type--</option>
                        <option v-for="staff_type in staff_types" :value='staff_type.id'>{{staff_type.name}}</option>
                    </select>
                    <has-error :form="batchForm" field="staff_type_id"></has-error> 
                </div>
            </div>
        </div>
        <button @click.prevent="editMode ? editBatch() : createBatch() " type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            batch: {},
            batchForm: new Form({
                end_date: '',
                staff_type_id: "",
                shift_type_id: '',
                start_date: '',
                tasks:[],
                patient_id: "",
                id: '',
            }),
            patient: {},
            patient_tasks: [],
            today: '',
        }
    },
    mounted() {
        const date =new Date().toJSON().slice(0, 10);
        this.today = date;
        Fire.$on('BatchDataFill', batch =>{
            this.batch = batch;
            this.batchForm.fill(batch);
            if(this.batch.patient != null){
                this.patient = batch.patient;
                this.batchForm.patient_id = this.batch.patient.id;
                this.patient_tasks = this.batch.patient.tasks;
                this.batchForm.tasks = [];
                for (let i = 0; i < batch.activities.length; i++) {
                    this.batchForm.tasks.push(batch.activities[i].patient_task_id);
                }
            }
        });
        Fire.$on('AfterCreation', ()=>{
            //axios.get("api/profile").then(({ data }) => (this.ApplicantData.fill(data)));
        });
    },
    methods:{
        createBatch(){
            this.$Progress.start();
            this.batchForm.post('/api/emr/domiciliary/batch_tasks')
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('refreshBatchTask', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Batch Shift has been created',
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
        editBatch(){
            this.$Progress.start();
            this.batchForm.put('/api/emr/domiciliary/batch_tasks/'+this.batchForm.id)
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('refreshBatchTask', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Batch Shift has been modified',
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
        refresh(response){
            this.patient_tasks = response.data.patient_tasks;
        },
    },
    props:{
        applicant: Object,
        editMode: Boolean,   
        nations: Array, 
        patients: Array,
        staff_types: Array,
        shift_types: Array,
    }
}
</script>