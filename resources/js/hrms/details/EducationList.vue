<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="educationFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">Update Education Details</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormEducation :editMode.sync="editMode" :education.sync="employee" @refreshEducation="refreshEducationPage"/>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Type</th>
                <th>Education</th>
                <th>Institution</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="educations.length > 0">
            <tr v-for="(education, index) in educations" :key="education.id">
                <td>{{ addOne(index) }}</td>
                <td>{{ education.qualification != null ? education.qualification.name : 'Not Applicable' }}</td>
                <td>{{ education.details }}</td>
                <td>{{ education.institution }} <br /><span class="text-muted" v-html="education.address"></span></td>
                <td>{{ education.start_month != null ? ExcelMonthYear(education.start_month) : 'Not Applicable' }} - {{ education.end_month != null ? ExcelMonthYear(education.end_month) : 'Ongoing' }}</td>
                <td><button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="dropdown-item btn btn-block btn-sm" @click="viewEducation(education)"><i class="fa fa-eye mr-1 text-primary"></i> View Education</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateEducation(education)"><i class="fa fa-edit mr-1 text-warning"></i> Update Education</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="deleteEducation(education)"><i class="fa fa-user mr-1 text-danger"></i> Delete Education</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan="6">No Education was found</td></tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            education: {},
            loading: false,
        }
    },
    emits: ['refreshEducationPage'],
    methods:{
        closeModals(){
            $('#educationModal').modal('hide');
            $('#educationFormModal').modal('hide');
        },
        deleteEducation(id){
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
                    this.loading = true;
                    this.form.delete('/api/hrms/educations/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        updateEducation(education){
            this.education = education;
            this.editMode = true;
            $('#educationFormModal').modal('show');
        },
        refreshEducationPage(response){
            this.$emit('refreshEducationsPage');
            this.closeModals();
        },
        viewEducation(education){
            this.education = education;
            $('#educationModal').modal('show');
        }
    },
    mounted(){ 
        //this.getAllInitials();
    },
    props:{
        educations: Array,
        employee: Object,
        source: String,
        style: String,
    }
}
</script>