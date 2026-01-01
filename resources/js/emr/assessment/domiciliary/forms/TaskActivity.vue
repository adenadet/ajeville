<template>
<section>
    <form>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" class="form-control" id="date" name="date" placeholder="First Name *" v-model="activityData.date" :class="{'is-invalid' : activityData.errors.has('date') }">
                    <has-error :form="activityData" field="date"></has-error> 
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Time</label>
                    <input type="time" class="form-control" id="time" name="time" placeholder="middle Name" v-model="activityData.time" :class="{'is-invalid' : activityData.errors.has('time') }">
                    <has-error :form="activityData" field="time"></has-error> 
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Patient</label>
                    <select class="form-control" id="patient_id" name="patient_id" placeholder="First Name *" v-model="activityData.patient_id" :class="{'is-invalid' : activityData.errors.has('patient_id') }">
                        <option value="">--Select Patient--</option>
                        <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{patient.first_name+' '+patient.last_name}}</option>
                    </select>
                    <has-error :form="activityData" field="patient_id"></has-error> 
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Activity Type</label>
                    <select class="form-control" id="task_id" name="task_id" placeholder="middle Name" v-model="activityData.task_id" :class="{'is-invalid' : activityData.errors.has('task_id') }">
                        <option value="">--Select Task--</option>
                        <option v-for="task in tasks" :key="task.id" :value="task.id">{{task.name}}</option>
                    </select>
                    <has-error :form="activityData" field="task_id"></has-error> 
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Participated</label>
                    <select class="form-control" id="participated" name="participated" placeholder="middle Name" v-model="activityData.participated" :class="{'is-invalid' : activityData.errors.has('participated') }">
                        <option value="">--Please Select--</option>
                        <option value="participated">Participated</option>
                        <option value="refused">Refused</option>
                    </select>
                    <has-error :form="activityData" field="participated"></has-error> 
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Notes</label>
                    <wysiwyg id="notes" name="notes" v-model="activityData.notes" :class="{'is-invalid' : activityData.errors.has('notes') }" />
                    <has-error :form="activityData" field="notes"></has-error> 
                </div>
            </div>
        </div>
        <button class="btn btn-sm btn-primary" >Save</button>
        <button class="btn btn-sm btn-danger" >Cancel</button>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            taskForm: new Form({
                date: '',
                time: '',
                task_id: '',
                patient_id: '',
                participated: '',
                notes: '',
                id: '',
            }),
        }
    },
    mounted() {
        const date =new Date().toJSON().slice(0, 10);
        this.today = date;
        Fire.$on('ApplicantDataFill', user =>{
            this.ApplicantData.fill(user);
        });
        Fire.$on('AfterCreation', ()=>{
            //axios.get("api/profile").then(({ data }) => (this.ApplicantData.fill(data)));
        });
    },
    methods:{
        createRequest(){
            this.$Progress.start();
            this.requestForm.post('/api/emr/tasks/initials')
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('refreshAppointment', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Tasks details has been created',
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
        updateApplicantData(){
            console.log("Tested");
            this.$Progress.start();
            this.ApplicantData.put('/api/hims/patients/'+this.ApplicantData.id)
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('refreshAppointment', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Profile details has been updated',
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
        getProfilePic(){
            let photo = (this.ApplicantData.image.length >= 150) ? this.ApplicantData.image : "./"+this.ApplicantData.image;
            return photo;
            },
        updateProfilePic(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['size'] < 2000000){
                reader.onloadend = (e) => {
                    this.ApplicantData.image = reader.result
                    }
                reader.readAsDataURL(file)
            }
            else{
                Swal.fire({
                    type: 'error',
                    title: 'File is too large'
                })
            }
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