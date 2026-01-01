<template>
<section>
    <form @submit.prevent="editMode ? updateCategory() : createCategory()">
        <alert-error :form="categoryData"></alert-error> 
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="vendor_id">Name:</label>
                    <input type="text" name="name" id="name" class="form-control" v-model="categoryData.name" placeholder="Name" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select class="form-control" v-model="categoryData.status" id="status">
                        <option value="">--Select Status--</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="status">Description:</label>
                    <QuillEditor class="form-control"content-type="html" v-model:content="categoryData.description" id="description" name="description" placeholder="Description" />
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</section>
</template>

<script>
import { QuillEditor } from '@vueup/vue-quill';

export default {
    data() {
        return {
            categoryData: new Form({
                description: '',
                id: '',
                name: '',
                status: '',
            }),
            loading: false,
        };
    },
    emits:['vendorCategoryReload'],
    methods: {
        createCategory() {
            this.loading = true;
            this.categoryData.post('/api/procurement/vendor_categories')
            .then(response =>{
                this.loading = false;
                this.$emit('vendorCategoryReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Vendor Category has been created',
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
                this.loading = false;
            });
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/procurement/vendors/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Vendor Category Form did not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.vendors = response.data.vendors
        },
        updateCategory(){
            this.loading = true;
            this.categoryData.put('/api/procurement/vendor_categories/'+this.categoryData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('vendorCategoryReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Vendor Category has been updated',
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
                this.loading = false;
            });
        }
    },
    mounted() {
        this.getAllInitials();
    },
    props:{
        category: Object,
        editMode: Boolean,
        vendor: Object,
    },
    watch:{
        category(){
            this.categoryData.reset();
            if ((this.category != null) && (this.category.id != null)){
                this.categoryData.fill(this.category);
            }
        }
    }
};
</script>