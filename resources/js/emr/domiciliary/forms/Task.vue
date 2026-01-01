<template>
<section>
    <form>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Task Type</label>
                    <select class="form-control" id="task_id" name="task_id" placeholder="middle Name" v-model="taskData.task_id" :class="{'is-invalid' : taskData.errors.has('task_id') }">
                        <option value="">--Select Task--</option>
                        <option v-for="task in tasks" :key="task.id" :value="task.id">{{task.name}}</option>
                    </select>
                    <has-error :form="taskData" field="task_id"></has-error> 
                </div>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            taskForm: new Form({
                details: '',
                icon: '',
                id: '',
                name: '',
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