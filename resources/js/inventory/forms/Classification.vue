<template>
<section>
    <form>
        <alert-error :form="ClassificationData"></alert-error> 
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="ClassificationData.name" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="ClassificationData.status">
                        <option value="">--Select Parent Classification--</option>
                        <option value=1>Active</option>
                        <option value=0>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor content-type="html" theme="snow" id="description" name="description" v-model:content="ClassificationData.description" />
                </div>
            </div>
        </div>
        <input type="hidden" name="id" id="id" v-model="ClassificationData.id" />
        <button @click.prevent="editMode ? updateClassification() : createClassification()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            categories: [],
            item_types: [],
            ClassificationData: new Form({
                description: '',
                id: '',
                name: '', 
                status: '',
            }),
        }
    },
    emits: ['reloadClassification'],
    mounted() {},
    methods:{
        createClassification(){
            this.loading = true;
            this.ClassificationData.post('/api/inventory/classifications')
            .then(response =>{
                this.loading = false;
                this.$emit('reloadClassification');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Classification has been created',
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
            axios.get('/api/inventory/classifications/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Classification Form not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.categories = response.data.classifications;
            this.item_types = response.data.item_types;
        },
        updateClassification(){
            this.loading = true;
            this.ClassificationData.put('/api/inventory/classifications/'+this.ClassificationData.id)
            .then(response =>{
                this.$emit('reloadClassification');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Classification has been updated',
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
        classification: Object,        
        editMode: Boolean,
    },
    watch:{
        classification(){
            this.ClassificationData.description = '';
            this.ClassificationData.reset();
            this.ClassificationData.fill(this.classification);
        }
    }
}
</script>