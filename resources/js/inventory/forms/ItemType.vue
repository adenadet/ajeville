<template>
<section>
    <form>
        <alert-error :form="ItemTypeData"></alert-error> 
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="ItemTypeData.name" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="ItemTypeData.status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor content-type="html" theme="snow" id="description" name="description" v-model:content="ItemTypeData.description" />
                </div>
            </div>
        </div>
        <input type="hidden" name="id" id="id" v-model="ItemTypeData.id" />
        <button @click.prevent="editMode ? updateItemType() : createItemType()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            categories: [],
            types: [],
            ItemTypeData: new Form({
                name: '', 
                description: '',
                id: '',
                status: 1,
            }),
        }
    },
    emits:['reloadItemType'],
    methods:{
        createItemType(){
            this.loading = true;
            this.ItemTypeData.post('/api/inventory/item_types')
            .then(response =>{
                this.$emit('reloadItemType');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Item Type has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
            });  
            this.loading = false;
        },
        updateItemType(){
            this.loading = true;
            this.ItemTypeData.put('/api/inventory/item_types/'+this.ItemTypeData.id)
            .then(response =>{
                this.$emit('reloadItemType');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Item Type has been updated',
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
    mounted() {},
    props:{
        editMode: Boolean,
        item_type: Object,
    },
    watch:{
        item_type(){
            this.ItemTypeData.reset();
            if(this.editMode){
                this.ItemTypeData.fill(this.item_type);
            } else {
                this.ItemTypeData.reset();
            }
        }
    }
}
</script>