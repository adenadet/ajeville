<template>
<section class="overlay-wrapper p-0 m-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateDepartment() : createDepartment()">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" required class="form-control" id="name" name="name" placeholder="Name *" v-model="departmentData.name" :class="{'is-invalid' : departmentData.errors.has('name') }">
                    <has-error :form="departmentData" field="name"></has-error>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Head of Department</label>
                    <select required class="form-control" id="hod_id" name="hod_id" placeholder="Select HOD *" v-model="departmentData.hod_id" :class="{'is-invalid' : departmentData.errors.has('hod_id') }">
                        <option value="">--Select Head of Department---</option>
                        <option v-for="employee in employees" :value="employee.id">{{ FullName(employee.user) }}</option>                      
                    </select>
                    <has-error :form="departmentData" field="hod_id"></has-error>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Extension/Phone number</label>
                    <input type="text" required class="form-control" id="ext" name="ext" placeholder="Phone *" v-model="departmentData.ext" :class="{'is-invalid' : departmentData.errors.has('ext') }">
                    <has-error :form="departmentData" field="ext"></has-error>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" required class="form-control" id="email" name="email" placeholder="Phone *" v-model="departmentData.email" :class="{'is-invalid' : departmentData.errors.has('email') }">
                    <has-error :form="departmentData" field="email"></has-error>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Status</label>
                    <select required class="form-control" id="status" name="status" v-model="departmentData.status" :class="{'is-invalid' : departmentData.errors.has('status') }">
                        <option value="">--Select any status--</option>
                        <option value=1>Active</option>
                        <option value=0>Inactive</option>
                    </select>
                    <has-error :form="departmentData" field="status"></has-error>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor content-type="html" theme="snow" class="form-control" id="description" name="description" placeholder="Description" v-model:content="departmentData.description" :class="{'is-invalid' : departmentData.errors.has('description') }"/>
                    <has-error :form="departmentData" field="description"></has-error>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12"><button class="btn btn-primary" type="submit">Submit </button></div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            departmentData: new Form({
                description: '',
                email: '',
                ext: '',
                hod_id: '',
                id: '',
                name: '', 
                status: '',
            }),
            employees: [],
            loading: false,
            users: [],
        }
    },
    emits: ['reloadDepartmentForm'],
    mounted() {
        this.getAllInitials();
    },
    methods:{
        createDepartment(){
            this.loading = true;
            this.BranchPriceListData.post('/api/operations/departments')
            .then(response =>{
                this.loading = false;
                this.$emit('reloadDepartmentForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Branch has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
                this.loading = false;
            });  
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/operations/departments/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'BranchPriceList Form not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.employees = response.data.employees;
            this.categories = response.data.categories;
            this.item_types = response.data.item_types;
            this.users = response.data.users;
        },
        updateDepartment(){
            this.loading = true;
            this.BranchPriceListData.put('/api/operations/departments/'+this.departmentData.id)
            .then(response =>{
                this.$emit('reloadDepartmentForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The BranchPriceList has been updated',
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
            }); 
            this.loading = false;                 
        },
    },
    props:{
        department: Object,
        editMode: Boolean,        
    },
    watch:{
        department(){
            this.departmentData.fill(this.department);
        }
    }
}
</script>