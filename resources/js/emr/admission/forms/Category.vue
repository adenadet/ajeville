<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateCategory() : createCategory()">
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter category name" v-model="categoryData.name" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" id="status" placeholder="Enter category status" v-model="categoryData.status" required>
                        <option value="" disabled>--Select status--</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div> 
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description">Description</label>
                    <QuillEditor class="form-control" name="description" id="description" placeholder="Enter category description" v-model:content="categoryData.description" rows="3" content-type="html" />
                </div>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">{{ editMode ? 'Update' : 'Create' }} Category</button>
            </div>  
        </div>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            categoryData: new Form({
                description: '',
                id: '',
                item_id: '',
                item: {},
                name: '',
                status: '',
            }),
            loading: false,
        }
    },
    emits: ['refreshCategoryForm'],
    methods: {
        createCategory() {
            this.loading = true;
            this.categoryData.post('/api/emr/admissions/categories')
            .then(() => {

                this.$swal.fire({ icon: 'success', title: 'The Category has been created', showConfirmButton: false, timer: 1500 });
                this.$emit('refreshCategoryForm');
                this.categoryData.reset();
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            })
            .finally(() => {
                this.loading = false;
            });
        },
        getInitials() {
            axios.get('/api/emr/admissions/categories/initials')
            .then((response) => {
                this.refreshPage(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Category Form was loaded successfully',
                })
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Category Form was not loaded successfully',
                })
            });
        },
        updateCategory(){
            this.loading = true;
            this.categoryData.put('/api/emr/admissions/categories/' + this.categoryData.id)
            .then(response => {
                this.$swal.fire({ icon: 'success', title: 'The Category has been updated', showConfirmButton: false, timer: 1500 });
                this.$emit('refreshCategoryForm');
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
        //this.getInitials();
    },
    props: {
        category: Object,
        editMode: {type: Boolean, default: false},
    },
    watch:{
        category(){
            if (this.editMode) {
                this.categoryData.fill(this.category);
            }
            else{
                this.categoryData.reset();
            }
        }
    }
}
</script>