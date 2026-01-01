<template>
<section>
    <form>
        <alert-error :form="categoryData"></alert-error> 
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="categoryData.name" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Parent</label>
                    <select class="form-control" id="name" name="name" v-model="categoryData.parent_category_id">
                        <option value="">--Select Parent Category--</option>
                        <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Type</label>
                    <select class="form-control" id="name" name="name" v-model="categoryData.type_id">
                        <option value="">--Select Type--</option>
                        <option v-for="item_type in item_types" :value="item_type.id">{{ item_type.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor rows="5" id="description" name="description" v-model:content="categoryData.description" content-type="html"/>
                </div>
            </div>
        </div>
        <input type="hidden" name="id" id="id" v-model="categoryData.id" />
        <button @click.prevent="editMode ? updateCategory() : createCategory()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </form>  
</section>
</template>
<script>
import { QuillEditor } from '@vueup/vue-quill';

export default {
    data(){
        return  {
            categories: [],
            item_types: [],
            categoryData: new Form({
                description: '',
                id: '',
                name: '', 
                parent_category_id: '',
                location: '',
                status: '',
                type_id: '',
            }),
        }
    },
    emits: ['reloadCategory'],
    mounted() {
        this.getInitials();
    },
    methods:{
        createCategory(){
            this.loading = true;
            this.categoryData.post('/api/inventory/categories')
            .then(response =>{
                this.loading = false;
                this.$emit('reloadCategory');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Category has been created',
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
        getInitials(){
            this.loading = true;
            axios.get('/api/archives/categories/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Category not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.categories = response.data.categories;
            this.item_types = response.data.item_types;
        },
        updateItemType(){
            this.loading = true;
            this.categoryData.put('/api/inventory/categories/'+this.categoryData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('reloadCategory');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Category has been updated',
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
    },
    props:{
        category: Object,        
        editMode: Boolean,
    },
    watch:{
        category(){
            this.categoryData.fill(this.category);
        }
    }
}
</script>