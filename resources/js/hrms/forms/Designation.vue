<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateDesignation() : createDesignation()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Training Name" v-model="designationData.name" >
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Department</label>
                    <select class="form-control" name="department_id" id="department_id" v-model="designationData.department_id">
                        <option value="">--Select Department---</option>
                        <option v-for="department in departments" :value="department.id">{{ department.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" id="status"  v-model="designationData.status">
                        <option value="">--Select Status---</option>
                        <option value=1>Active</option>
                        <option value=0>Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor v-model:content="designationData.description" theme="snow" content-type="html" class="form-control"></QuillEditor>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-primary mt-3" type="submit">{{editMode ? 'Update' : 'Create'}}</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            departments: [],
            designationData : new Form({
                id: '',
                name: '',
                department_id: '',
                status: '',
                description: '',
            }),
            loading: false,
        }
    },
    emits: ['refreshDesignation'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        createDesignation() {
            this.loading = true;
            this.designationData.post('/api/hrms/designations')
                .then(response => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Designation created successfully!',
                    });
                    this.$emit('refreshDesignation', response);
                    this.loading = false;
                })
                .catch(error => {
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong while creating education.',
                    });
                    this.loading = false;
                });
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/hrms/designations/initials')
            .then(response =>{
                this.refreshPage(response);
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Designation Form not loaded successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response){
            this.departments = response.data.departments;
        },
        updateDesignation() {
            this.loading = true;
            console.log(this.designationData.id);
            this.designationData.put(`/api/hrms/designations/${this.designationData.id}`)
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Designation updated successfully!',
                });
                this.$emit('refreshDesignation', response);
                this.loading = false;
            })
            .catch(error => {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong while updating designation.',
                });
                this.loading = false;
            });
        },

    },
    props: {
        designation: Object,
        editMode: Boolean,
        source: String,
    },
    watch: {
        designation(){
            this.loading = true;
            this.designationData.fill(this.designation);
            this.loading = false;
        }
    }
}
</script>