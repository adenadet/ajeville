<template>
<section>
    <form>
        <alert-error :form="brandData"></alert-error> 
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="brandData.name" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="brandData.status">
                        <option value="">--Select Status--</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor rows="5" content-type="html" id="description" name="description" v-model:content="brandData.description"></QuillEditor>
                </div>
            </div>
        </div>
        <input type="hidden" name="id" id="id" v-model="brandData.id" />
        <button @click.prevent="editMode ? updateBrand() : createBrand()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            brands: [],
            item_types: [],
            brandData: new Form({
                description: '',
                id: '',
                name: '',
                status: '',
            }),
        }
    },
    emits: ['reloadBrand'],
    mounted() {},
    methods:{
        createBrand(){
            this.loading = true;
            this.brandData.post('/api/inventory/brands')
            .then(response =>{
                this.$emit('reloadBrand');
                this.$swal.fire({icon: 'success', title: 'The Brand has been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });  
        },
        refreshPage(response){
            this.brand = response.data.brand;
        },
        updateBrand(){
            this.loading = true;
            this.brandData.put('/api/inventory/brands/'+this.brandData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('reloadBrand');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Brand has been updated',
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
        brand: Object,        
        editMode: Boolean,
    },
    watch:{
        brand(){
            if (this.brand != null && this.brand.id != null){this.brandData.fill(this.brand);}
            else{this.brandData.reset();}
        }
    }
}
</script>