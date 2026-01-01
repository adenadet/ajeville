<template>
    <div class="overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <form @submit.prevent="createPolicy"  enctype="multipart/form-data">
            <alert-error :form="policyData"></alert-error> 
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Policy Name {{ policy }}</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name *" v-model="policyData.name" required>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Category</label>
                        <select class="form-control" id="category_id" name="category_id" v-model="policyData.category_id">
                            <option value="">---Select Policy Type---</option>
                            <option value="0">General</option>
                            <option value="1">Departmental </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row" v-if="policyData.category_id == '1'">
                <div class="col-sm-12">
                    <div class="form-group"><label>Departments</label></div>
                </div>
                <div class="col-sm-3" v-for="department in departments" :key="department.id">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="departments[]" id="departments[]" 
                        v-model="policyData.departments" :value="department.id" 
                        :checked="policyData.departments.includes(department.id)">
                        <label class="form-check-label">{{department.name}}</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" v-if="!editMode">
                    <div class="form-group">
                        <label>Upload File</label>
                        <input type="file" class="form-control" id="document" name="document" v-on:change="updatePolicyFile"/> 
                    </div>
                </div>
                <div class="col-sm-6" v-if="editMode">
                    <div class="form-group">
                        <label>Upload File</label>
                        <input type="file" class="form-control" id="document" name="document" v-on:change="updatePolicyFile"/> 
                    </div>
                </div>
                <div class="col-sm-6" v-if="editMode">
                    <div class="form-group">
                        <label>Existing File</label>
                        <div class="form-control">{{policyData.file != null ? policyData.file: "No File Added Yet"}}</div> 
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" v-model="policyData.description"></textarea>
                    </div>
                </div>
                <input type="hidden" name="editMode" id="editMode" :value="editMode" />
                <div class="col-md-4 col-sm-12">
                    <input type="submit" name="submit" class="submit btn btn-success" :value="editMode ? 'Update' : 'Create'" />
                </div>
            </div>
        </form>
    </div>
</template>
<script>
export default {
    data(){
        return {   
            departments: [],
            file: '',
            filename: '',
            loading: false,
            policyData: new Form({
                id:'', 
                category_id: '', 
                departments: [],
                description: '',
                file: '', 
                name:'', 
            }),
        }
    },
    emits:['reloadPolicies'],
    methods:{
        onFileChange(e){
            this.filename = "Selected File: " + e.target.files[0].name;
            this.file = e.target.files[0];
        },
        createPolicy(){
            this.loading = true;
            this.policyData.post('/api/policies')
            .then(response =>{
                this.loading = false;
                this.$emit('Reload', response);
                this.$emit('reloadPolicies');
                this.$swal.fire({icon: 'success', title: 'The Policy has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.loading = false;
            });
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/policies/initials')
            .then(response => {
                this.departments = response.data.departments; 
                this.loading = false;       
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Policy form did not loaded successfully',})
                this.loading = false;
            });
        },
        uploadFile(e){
            this.file = e.target.files[0];
        },
        uploadFiles(e) {
            e.preventDefault();
            
            const json = JSON.stringify({
                id: this.policyData.id, 
                category_id: this.policyData.category_id, 
                description: this.policyData.description,
                name: this.policyData.name, 
                editMode: this.editMode,
            });
            let currentObj = this;
            
            const config = {headers: { 'content-type': 'multipart/form-data'}}

            let formData = new FormData();
            if (!(this.editMode)){formData.append('file', this.file);}
            formData.append('data', json);

            console.log(formData.data);
            if (editMode){
                axios.put('/api/policies/'+this.policyData.id, formData, config)
                .then(function (response) {
                    this.$emit('CourseRefresh', response.data.course);
                    this.$swal.fire({icon: 'success', title: response.data.success,});   
                })
                .catch(function (error) {
                    currentObj.output = error;
                });
            }
            else{
                axios.post('/api/policies', formData, config)
                .then(function (response) {
                    //Fire.$emit('refresh', response);
                    this.$emit('CourseRefresh', response.data.course);
                    this.$swal.fire({icon: 'success', title: response.data.success,});   
                })
                .catch(function (error) {
                    currentObj.output = error;
                });
            } 
        },
        updatePolicy(){
            this.loading = true;
            this.policyData.put('/api/policies/'+this.policyData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('Reload', response);
                this.$emit('reloadPolicies');
                this.$swal.fire({icon: 'success', title: 'The Policy has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.loading = false;
            });
        },
        updatePolicyFile(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file.type != 'application/pdf' ) {
                this.$swal.fire({icon: 'error', title: 'File must be pdf'});
                return;
            }
            else if (file['size'] > 2000000){
                this.$swal.fire({icon: 'error', title: 'File is too large'}); 
                return;
            }
            else{
                reader.onloadend = (e) => {this.policyData.file = reader.result;}
                reader.readAsDataURL(file)
            }    
        },
    },
    mounted() {
        this.getInitials();
    },
    props: {
        editMode: Boolean,
        policy: Object,
    },
    watch:{
        policy(){
            alert(this.policy.name)
            this.policyData.reset();
            //this.policyData.fill(this.policy)
            this.policyData.id = this.policy.id 
            this.policyData.category_id = this.policy.category_id 
            this.policyData.departments = []
            this.policyData.description = this.policy.description
            this.policyData.file = this.policy.file
            this.policyData.name = this.policy.name
            //this.policyData.departments = this.policy.depts.filter();
            //console.log(this.policy);
        }
    }
}
</script>