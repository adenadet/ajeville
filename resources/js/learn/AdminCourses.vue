<template>
    <div class="row overlay-wrapper p-0">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="modal fade" id="courseModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h4 class="modal-title">New Course</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <LearnFormCourse :course="course" :editMode="editMode" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">Courses</h3>
                    <div class="card-tools">
                        <div class="input-group input-group" style="width: 500px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary mr-1" @click="getInitials(1)"><i class="fas fa-search"></i></button>
                                <select class="form-control" v-model="status" @change="getInitials(1)">
                                    <option value="0">Inactive</option>
                                    <option value="1">Active</option>
                                    <option value="2">Approved</option>
                                    <option value="3">Ongoing</option>
                                    <option value="4">Completed</option>
                                    <option value="all">All</option>
                                </select>
                                <button type="button" class="btn btn-primary ml-1" @click="addCourse()"><i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-success ml-1" @click="uploadOrders()"><i class="fa fa-upload"></i></button>
                                <button type="button" class="btn btn-info ml-1" @click="downloadOrders()"><i class="fa fa-download"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive" style="height: 500px;">
                    <LearnDetailCourseList :courses="courses.data" source="admin" @refreshCourses="getAllInitials" />
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="courses.per_page != null ? courses.per_page : 52" :records="courses.total != null ? courses.total : 550" ></pagination>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {
            course:{},
            courses:{},
            current_page: 1,
            editMode: false,
            loading:false,
        }
    },
    methods:{
        addCourse(){
            this.loading = true;
            this.editMode = false;
            this.course = {};
            $('#courseModal').modal('show');
            this.loading = false;
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/learn/courses').then(response =>{
                this.refresh(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Courses were loaded successfully',
                });
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Courses were not loaded successfully',
                })
            });
            this.loading = false;
        },
        refresh(response){
            this.courses = response.data.courses;
        },
    },
    mounted() {
        this.getAllInitials();
    }
}
</script>