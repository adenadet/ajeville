<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateService() : createService()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter service name" v-model="serviceData.name" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" name="category_id" id="category_id" placeholder="Enter service category_id" v-model="serviceData.category_id" required>
                        <option value="" disabled>--Select status--</option>
                        <option v-for="category in categories":value="category.id" :key="category.id">{{ category.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" id="status" placeholder="Enter service status" v-model="serviceData.status" required>
                        <option value="" disabled>--Select status--</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div> 
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description">Description</label>
                    <QuillEditor class="form-control" name="description" id="description" placeholder="Enter service description" v-model:content="serviceData.description" rows="3" content-type="html" />
                </div>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">{{ editMode ? 'Update' : 'Create' }} Service</button>
            </div>  
        </div>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            categories: [],
            serviceData: new Form({
                description: '',
                id: '',
                service_id: '',
                name: '',
                status: '',
            }),
            loading: false,
        }
    },
    emits: ['refreshServiceForm'],
    methods: {
        createService() {
            this.loading = true;
            this.serviceData.post('/api/emr/admissions/services')
            .then(() => {
                this.$swal.fire({ icon: 'success', title: 'The Service has been created', showConfirmButton: false, timer: 1500 });
                this.$emit('refreshServiceForm');
                this.serviceData.reset();
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            })
            .finally(() => {
                this.loading = false;
            });
        },
        getInitials() {
            axios.get('/api/emr/admissions/services/initials')
            .then((response) => {
                this.categories = response.data.categories;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Service Form was loaded successfully',
                })
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Service Form was not loaded successfully',
                })
            });
        },
        updateService(){
            this.loading = true;
            this.serviceData.put('/api/emr/admissions/services/' + this.serviceData.id)
            .then(response => {
                this.$swal.fire({ icon: 'success', title: 'The Service has been updated', showConfirmButton: false, timer: 1500 });
                this.$emit('refreshServiceForm');
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
        service: Object,
        editMode: {type: Boolean, default: false},
    },
    watch:{
        service(){
            if (this.editMode) {
                this.serviceData.fill(this.service);
            }
            else{
                this.serviceData.reset();
            }
        }
    }
}
</script>