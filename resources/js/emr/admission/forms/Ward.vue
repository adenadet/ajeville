<template>
<section class="overlay-wrapper p-0">
    <form @submit.prevent="editMode ? updateWard() : createWard()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter ward name" v-model="wardData.name" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Branch</label>
                    <select class="form-control" name="branch_id" id="branch_id" placeholder="Enter ward branch_id" v-model="wardData.branch_id" required>
                        <option value="" disabled>--Select Branch--</option>
                        <option v-for="branch in branches" :value="branch.id" :key="branch.id">{{ branch.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" id="status" placeholder="Enter ward status" v-model="wardData.status" required>
                        <option value="" disabled>--Select status--</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description">Description</label>
                    <QuillEditor class="form-control" name="description" id="description" placeholder="Enter ward description" v-model:content="wardData.description" rows="3" content-type="html" />
                </div>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">{{ editMode ? 'Update' : 'Create' }} Ward</button>
            </div>
        </div>
    </form>    
</section>
</template>
<script>
export default {
    data() {
        return {
            branches: [],
            loading: false,
            wardData: new Form({
                branch_id: '',
                description: '',
                id: '',
                name: '',
                status: '',
            }),
        }
    },
    emits: ['refreshWardForm'],
    methods: {
        createWard() {
            this.loading = true;
            this.wardData.post('/api/emr/admissions/wards')
            .then(() => {
                this.$swal.fire({ icon: 'success', title: 'The Ward has been created', showConfirmButton: false, timer: 1500 });
                this.$emit('refreshWardForm');
                this.wardData.reset();
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            })
            .finally(() => {
                this.loading = false;
            });
        },
        getInitials() {
            axios.get('/api/emr/admissions/wards/initials')
            .then((response) => {
                this.branches = response.data.branches;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Ward Form was loaded successfully',
                })
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Ward Form was not loaded successfully',
                })
            });
        },
        updateWard(){
            this.loading = true;
            this.wardData.put('/api/emr/admissions/wards/' + this.wardData.id)
            .then(response => {
                this.$swal.fire({ icon: 'success', title: 'The Ward has been updated', showConfirmButton: false, timer: 1500 });
                this.$emit('refreshWardForm');
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            })
            .finally(() => {
                this.loading = false;
            });
        },

    },
    mounted() {
        this.getInitials();
    },
    props: {
        editMode: {type: Boolean, default: false},
        ward: {type: Object, default: () => ({})},
    },
    watch:{
        ward(){
            if (this.editMode) {
                this.wardData.fill(this.ward);
            }
            else{
                this.wardData.reset();
            }
        }
    }
}
</script>