<template>
    <section class="card overlay-wrapper p-0">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <form role="form" @submit.prevent="editMode ? updateLeaveRequest() : createLeaveRequest()">
            <div class="card-body">
                <div class="row" v-if="source != 'mine'">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Employee</label>
                            <div class="form-control" v-if="editMode">{{ FullName(employee.user) }}</div>
                            <model-list-select class="form-control" :list="employees" v-model="leaveRequestData.employee_id" option-value="id" :custom-text="codeAndNameAndDesc" placeholder="Select Applicant" />
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Leave Type</label>
                            <div  v-if="editMode" class="form-control">{{ leave_request.leave_type != null ? leave_request.leave_type.name : 'Undecided' }}</div>
                            <select class="form-control" v-model="leaveRequestData.leave_type_id">
                                <option value="">--Select Leave Type---</option>
                                <option v-for="user_leave_type in user_leave_types" :value="user_leave_type.id">{{ user_leave_type.leave_type.name }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="source == 'mine'">
                    <div class="col-sm-12" >
                        <div class="form-group">
                            <label>Leave Type</label>
                            <select v-if="editMode" class="form-control" name="leave_type_id" id="leave_type_id" v-model="leaveRequestData.user_leave_type_id" required>
                                <option value="">--Choose Leave Type--</option>
                                <option v-for="user_leave_type in leave_types" :key="user_leave_type.id" :value="user_leave_type.id">{{ user_leave_type.leave_type.name }} ({{ user_leave_type.days_used }} days used out of {{ user_leave_type.leave_type.no_of_days }} days)</option>
                            </select>
    
                            <select v-else class="form-control" name="leave_type_id" id="leave_type_id" v-model="leaveRequestData.leave_type_id" required>
                                <option value="">--Choose Leave Type--</option>
                                <option v-for="user_leave_type in leave_types" :key="user_leave_type.id" :value="user_leave_type.id">{{ user_leave_type.leave_type.name }} ({{ user_leave_type.days_used }} days used out of {{ user_leave_type.leave_type.no_of_days }} days)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input class="form-control" type="date" placeholder="Start Date" name="from_date" id="from_date" v-model="leaveRequestData.from_date" :min="to_day">
                            <has-error :form="leaveRequestData" field="from_date"></has-error> 
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>End Date</label>
                            <input class="form-control" type="date" placeholder="End Date" name="to_date" id="to_date" v-model="leaveRequestData.to_date" :min="leaveRequestData.from_date == '' || leaveRequestData.from_date == null ? to_day : leaveRequestData.from_date">
                            <has-error :form="leaveRequestData" field="to_date"></has-error> 
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Process Leave Allowance?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" v-model="leaveRequestData.leave_allowance">
                                <label class="form-check-label" :disabled="days_taken >= 2 ? 'disabled' : ''">Yes</label>
                            </div>
                        </div>
                    </div>
                    <div v-if="!editMode" class="col-sm-12 text-danger"><strong>{{ days_taken }} days taken</strong></div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> Reason </label>
                            <input type="text" name="reason" id="reason" class="form-control"  v-model="leaveRequestData.reason" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Attachment</label>
                            <input type="file" name="attachment" id="attachment" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label> Remarks </label>
                            <textarea type="text" name="remarks" id="remarks" class="form-control" rows=5 placeholder="Other Information" v-model="leaveRequestData.remarks"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                </div>     
            </div>
            <div class="card-footer">
                <button type="submit button" class="btn btn-info float-right" >{{ editMode ? 'Update' : 'Create' }} </button>
            </div>
        </form>
    </section>
</template>
<script>
import Form from 'vform';
export default {
    computed:{
        days_taken(){
            if (this.leave_types == null || this.leaveRequestData.leave_type_id == '' || this.leaveRequestData.leave_type_id == null || this.leaveRequestData.from_date == null || this.leaveRequestData.from_date == '' || this.leaveRequestData.to_date == '' || this.leaveRequestData.to_date == null){
                return 0;
            }
            else{
                let date1 = new Date(this.leaveRequestData.from_date);
                let date2 = new Date(this.leaveRequestData.to_date);
                var leave_type = this.leave_types.find(obj => obj.id === this.leaveRequestData.leave_type_id);
                if (leave_type == null || leave_type == undefined){ return 0;}
                else{
                    if (leave_type.leave_type.leave_category == "Calendar"){
                        let Difference_In_Time = date2.getTime() - date1.getTime();
                        let Difference_In_Days = Math.round(Difference_In_Time / (1000 * 3600 * 24));
                        return Difference_In_Days+1;
                    }
                    else if(leave_type.leave_type.leave_category == "Working"){
                        var day;
                        var current = date1;
                        var totalBusinessDays = 0;
                        while (current <= date2) {
                            day = current.getDay();
                            if (day >= 1 && day <= 5) {
                                ++totalBusinessDays;
                            }
                            current.setDate(current.getDate() + 1);
                        }
                        return totalBusinessDays;
                    }
                    else{return 0;}
                }
            }
        },
        max_day(){
            if (this.leave_types == null || this.leaveRequestData.leave_type_id == '' || this.leaveRequestData.leave_type_id == null){ return this.to_day;}
            else{
                var leave_type = this.leave_types.find(obj => obj.id === this.leaveRequestData.leave_type_id);
                if(leave_type == null){return this.to_day;}
                else if(this.leaveRequestData.from_date == '' || this.leaveRequestData.from_date == undefined){return this.to_day;}
                else{
                    var count = 0;
                    var curDate = new Date(this.leaveRequestData.from_date);
                    curDate.setDate(curDate.getDate() - 1);
                    while (count < (leave_type.leave_type.no_of_days - leave_type.days_used)){
                        curDate.setDate(curDate.getDate() + 1);
                        var dayOfWeek = curDate.getDay();
                        if(!((dayOfWeek == 6) || (dayOfWeek == 0))){count++;}
                    }
                    return curDate.toJSON().slice(0, 10);
                }
            }
        },
        to_day(){
            return new Date().toJSON().slice(0, 10);
        },
        user_leave_types(){
            if (this.source == 'admin'){
                if (this.leaveRequestData.employee_id == '' ||this.leaveRequestData.employee_id == null){
                    return [];
                }
                else{
                    var employee = this.employees.find(obj => obj.id === this.leaveRequestData.employee_id);
                    return employee.leave_types;
                }
            }
            else{
                return this.leave_types;
            }
        },
    },
    data() {
        return {
            employee: {},
            employees: [],
            leaveRequestData: new Form({
                id: '',
                leave_type_id: '',
                from_date: '',
                to_date: '',
                reason: '',
                remarks: '',
                is_half_day: false,
                leave_attachment: '',
                leave_allowance: false,
                user_leave_type_id: '',
            }),
            leave_request_id: 0,
            leave_type:{},
            leave_types: [],
            loading: false,
            services: [],
            user: {},
        }
    },
    emits:['refreshPage'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        codeAndNameAndDesc(item){
            return `${item.user.last_name}, ${item.user.first_name} ${item.user.middle_name} [${item.username}]`
        },
        createLeaveRequest(){
            this.loading = true;
            this.leaveRequestData.post('/api/hrms/leaves')
            .then(response =>{
                this.loading = false;
                this.$emit('refreshPage');
                this.$swal.fire({icon: 'success', title: 'The Leave Request has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });
        },
        deleteAppointment(id){
            this.$swal.fire({
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
                        this.$swal.fire('Deleted!', 'Appointment has been deleted.', 'success');
                        this.refreshAppointments(response);   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/hrms/leaves/initials')
            .then(response => {
                this.employees = response.data.employees;
                this.leave_types = response.data.my_leave_types;
                this.loading = false;
            })
            .catch(() => {
                toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
                this.loading = false;
            });
        },
        updateLeaveRequest(){
            this.loading = true;
            this.leaveRequestData.put('/api/hrms/leaves/'+this.leaveRequestData.id)
            .then(response =>{
                this.$emit('refreshPage');
                this.loading = false;
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Leave Request has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });
        },
        
    },
    props: {
        editMode: Boolean,
        leave_request: Object,
        source: String,
    },
    watch:{
        leave_request(){
            this.leaveRequestData.fill(this.leave_request);
            if(this.source == 'mine'){
                this.getAllInitials();
            }
        }
    }
}
</script>