<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>Assessment Period</th>
                <th>Employee</th>
                <th>Line Manager</th>
                <th>Status</th>
                <th>Score</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="assessments.length > 0">
            <tr v-for="assessment in assessments" :key="assessment.id">
                <td>{{ assessment.period != null ? assessment.period.name : 'Deactivated Period' }}</td>
                <td>{{ assessment.employee != null && assessment.employee.user != null ? FullName(assessment.employee.user) : 'Old Employee' }}</td>
                <td>{{ assessment.line_manager != null && assessment.line_manager.user != null ? FullName(assessment.line_manager.user) : 'Old Employee' }}</td>
                <td>{{ assessment.status }}</td>
                <td>{{ assessment.total_score+' / '+assessment.max_score }}</td>
                <td>
                    <button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'employee'">
                        <router-link :to="'/hrms/assessments/'+assessment.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Assessment</button></router-link>
                        <button v-if="assessment.status == 1" class="dropdown-item btn btn-block btn-sm" @click="remindLineManager(assessment.id)"><i class="fa fa-envelope mr-1 text-warning"></i> Remind Line Manger</button>
                    </div>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'admin'">
                        <router-link :to="'/hrms_admin/assessments/'+assessment.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Assessment</button></router-link>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateAssessmnet(assessment)"><i class="fa fa-edit mr-1 text-warning"></i> Update Record</button>
                        <button v-if="source == 'admin'" class="dropdown-item btn btn-block btn-sm" @click="deactivateAssessment(assessment.id)"><i class="fa fa-trash mr-1 text-danger"></i> Update Record</button>
                    </div>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'team'">
                        <router-link :to="'/hrms/assessments/'+assessment.id"><button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View Assessment</button></router-link>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateAssessmnet(assessment)"><i class="fa fa-edit mr-1 text-warning"></i> Update Record</button>
                        <button v-if="source == 'admin'" class="dropdown-item btn btn-block btn-sm" @click="deactivateAssessment(assessment.id)"><i class="fa fa-trash mr-1 text-danger"></i> Update Record</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <td colspan="6">No Assessment has been done on this Employee</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits: ['reloadAssessmentList'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        deactivateAssessment(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, clock me in!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/hrms/assessments/'+id)
                    .then(response=>{
                        this.$swal.fire('Success', 'Assessment has been deactivated/reactivated', 'success');
                        //this.getAllInitials();
                        this.$emit('reloadAssessmentList');
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        /*getAllInitials(){
            this.loading = true;
            if (this.start_date == '' || this.end_date == ''){
                var date = new Date();
                this.end_date = date.toISOString().split('T')[0];
                date.setDate(date.getDate() - 30);
                this.start_date = date.toISOString().split('T')[0];
            }
            
            axios.get('/api/hrms/clock_ins?type=mine&start='+this.start_date+'&end='+this.end_date).then(response =>{
                this.reset(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Clock Ins did not load successfully',});
            });
        },*/
        remindLineManager(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remind my line manager!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/hrms/assessments/'+id)
                    .then(response=>{
                        this.$swal.fire('Success', 'Line Manager has been notified', 'success');
                        //this.getAllInitials();
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        
        reset(response){
            this.clock_ins = response.data.clock_ins;
        },
        updateAssessment(assessment){

        }
    },
}
</script>