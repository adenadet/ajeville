<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="">
        <div class="body">
            <form @submit.prevent="editMode ? updateCourse() : createCourse() ">
                <alert-error :form="courseData"></alert-error> 
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name *" v-model="courseData.name" required>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <div class="form-group">
                            <label>Category</label>
                            <select class="form-control" id="category_id" name="category_id" v-model="courseData.category_id" required @change="changeSubCategory">
                                <option v-show="prev_cat" :value="prev_category.id">{{prev_category.name}}</option>
                                <option value="">--Select Category--</option>
                                <option v-for="(category,index) in categories" :key="index" :value="index">{{category.name}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <div class="form-group">
                            <label>Sub Category</label>
                            <select class="form-control" id="sub_category_id" name="sub_category_id" v-model="courseData.sub_category_id" required>
                                <option value="">--Select Sub Category--</option>
                                <option v-for="sub_category in sub_categories" :key="sub_category.id" :value="sub_category.id">{{sub_category.name}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label>Exam </label>
                            <select class="form-control" id="exam_type_id" name="exam_type_id" v-model="courseData.exam_type_id" required>
                                <option value="">--Select Exam Type--</option>
                                <option value="0">No Exam</option>
                                <option v-for="exam_type in exam_types" :key="exam_type.id" :value="exam_type.id">{{exam_type.name}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label>Certificate </label>
                            <select class="form-control" id="certificate_type_id" name="certificate_type_id" v-model="courseData.certificate_type_id" required>
                                <option value="">--Select Certificate Type--</option>
                                <option value="0">No Certificate</option>
                                <option v-for="certificate_type in certificate_types" :key="certificate_type.id" :value="certificate_type.id">{{certificate_type.name}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label>No of Modules </label>
                            <input type="number" class="form-control" id="lessons" name="lessons" placeholder="Number of Modules in the course*" v-model="courseData.lessons" required>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label>Cost </label>
                            <input type="number" class="form-control" id="price" name="price" placeholder="Estimated cost in naira, no comma or naira sign*" v-model="courseData.price" required>
                        </div>
                    </div>
                    <div class="col-md-8 col-sm-12">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" id="description" name="description" rows=5 placeholder="A full description of the Course" v-model="courseData.description"></textarea>
                        </div>
                    </div>
                </div>
                <input type="submit" name="submit" class="submit btn btn-success" value="Submit" />
            </form>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            categories: [],
            certificate_types: [],
            courseData: new Form({
                id:'',
                name:'', 
                category_id:'',
                certificate_id: 0, 
                sub_category_id: '', 
                description: '', 
                exam:'',
                lessons: '',
                price: '', 
                exam_type_id:'', 
                certificate:'', 
                certificate_type_id:'0', 
            }),
            exam_types: [],
            lecturers:[],
            loading: false,
            prev_cat: true,
            prev_category: {},
            sub_categories: [],
        }
    },
    emits:['refreshCourseForm'],
    methods:{
        changeSubCategory(){
            this.prev_cat = false;
            this.courseData.sub_category_id = '';
            this.sub_categories = this.categories[this.courseData.category_id].sub_categories;
        },
        createCourse(){
            this.loading = true;
            this.courseData.post('/api/learn/courses')
            .then(()=>{
                this.$emits('refreshCourseForm');
                this.$swal.fire({icon: 'success', title: 'Course has been created',});
                this.courseData.reset();
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Your form was not sent try again later!',});
            });
            this.loading = false;
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/learn/courses/initials')
            .then(response =>{
                this.refresh(response);
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Learn User Course not loaded successfully',})
            });
            this.loading = false;
        },
        refresh(response){
            this.categories = response.data.categories;
            this.certificate_types = response.data.categories;
            this.exam_types = response.data.categories;
            this.sub_categories = response.data.sub_categories;
        },
        updateCourse(){
            this.loading = true;
            this.courseData.put('/api/learn/courses/'+this.course.id)
            .then(()=>{
                this.$emits('refreshCourseForm');
                this.$swal.fire({icon: 'success', title: 'Course has been updated',});
                this.courseData.reset();
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Your form was not sent try again later!',});
            });
            this.loading = false;
        },
        
    },
    mounted() {
        this.getInitials();
    },
    props: {
        course: Object,
        editMode: Boolean,
    },
    watch:{
        course(){
            this.loading = true;
            this.courseData.fill(this.course);
            this.loading = false;
        }
    }
}
</script>