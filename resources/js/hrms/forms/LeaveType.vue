<template>
<section class="card overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="card-body">
        <form role="form">
            <alert-error :form="leaveTypeData"></alert-error> 
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Name</label>
                        <input class="form-control" type="text" name="name" id="name" v-model="leaveTypeData.name" />
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="form-group">
                        <label>Leave Category</label>
                        <select class="form-control" name="leave_category" id="leave_category" v-model="leaveTypeData.leave_category">
                            <option value="">--Select Leave Category--</option>
                            <option value="Calendar">Calendar</option>
                            <option value="Working">Working</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        <label>Number of Days</label>
                        <input class="form-control" type="number" name="no_of_days" id="no_of_days" v-model="leaveTypeData.no_of_days" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input class="form-control" type="date" name="start_date" id="start_date" v-model="leaveTypeData.start_date" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>End Date</label>
                        <input class="form-control" type="date" name="end_date" id="end_date" v-model="leaveTypeData.end_date" />
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" id="status" v-model="leaveTypeData.status">
                            <option value="">--Select Status--</option>
                            <option value="0">Active</option>
                            <option value="1">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Recurring</label>
                        <select class="form-control" name="recurring" id="recurring" v-model="leaveTypeData.recurring">
                            <option value="">--Select Recurring Category--</option>
                            <option value="never">Never</option>
                            <option value="annually">Annually</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4" v-if="leaveTypeData.recurring != 'never' && leaveTypeData.recurring != ''">
                    <div class="form-group">
                        <label>Recur Date</label>
                        <input class="form-control" type="text" name="date" id="date" v-model="leaveTypeData.date" />
                    </div>
                </div>
            </div>
            <button @click.prevent="editMode ? updateLeaveType() : createLeaveType()" type="button" name="submit" class="submit btn btn-primary">Submit</button>
        </form>
    </div> 
</section>
</template>
<script>
export default {
    data() {
        return {
            leaveTypeData: new Form({
                date: '', 
                end_date:'',
                leave_category: '', 
                name : '', 
                no_of_days: '', 
                recurring: '',
                start_date: '', 
                status: '',
                id: '', 
            }),
            loading: false,
            patients: [],
            services: [],
            user: {},
        }
    },
    emits:['refreshPage'],
    mounted() {},
    methods: {
        createLeaveType(){
            this.loading = true;
            this.leaveTypeData.post('/api/hrms/leave_types')
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Leave Request has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        updateLeaveType(){
            this.loading = true;
            this.leaveTypeData.put('/api/hrms/leave_types/'+this.leaveTypeData.id)
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Leave Request has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        deleteAppointment(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/emr/appointments/'+id)
                    .then(response=>{
                    Swal.fire('Deleted!', 'Appointment has been deleted.', 'success');
                    this.refreshAppointments(response);   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/hrms/leaves/initials')
            .then(response => {
                this.leave_types = response.data.my_leave_types;
                this.loading = false;
            })
            .catch(() => {
                toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
                this.loading = false;
            });
        },
        refreshLeaveRequest(response) {
            this.leave_types = response.data.my_leave_types;
        },
        resendAppointment(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "The candidate would get a mail with the confirmation letter",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, resend confirmation!'
                })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.get('/api/emr/registrations/resend/'+id)
                    .then(response=>{
                        //if (response.data.status == 'error')
                        Swal.fire(response.data.status, response.data.message, response.data.status);
                        //this.refreshAppointments(response);   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
    },
    props: {
        editMode: Boolean,
        leave_type: Object,
        source: String,
    },
    watch:{
        leave_type(){
            this.leaveTypeData.fill(this.leave_type);
        }
    }
}
</script>