<template>
<section>
    <form>
        <alert-error :form="vendorData"></alert-error> 
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="vendorData.name" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" id="category_id" name="category_id" v-model="vendorData.category_id">
                        <option value="">--Select Category--</option>
                        <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Website</label>
                    <input type="text" class="form-control" name="website" id="website" v-model="vendorData.website" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" id="email" v-model="vendorData.email" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="number" class="form-control" name="phone"  id="phone" v-model="vendorData.phone" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tax Identification Number (TIN)</label>
                    <input type="text" class="form-control" id="tin" name="tin" v-model="vendorData.tin">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Withholding Tax</label>
                    <input type="text" class="form-control" name="withholding_tax" id="withholding_tax" v-model="vendorData.withholding_tax" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Is Service VATable?</label>
                    <select class="form-control" name="vatable"  id="vatable" v-model="vendorData.vatable">
                        <option value="">--Select If VAT applies to Vendor--</option>
                        <option value="1">True</option>
                        <option value="0">False</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Address</label>
                    <QuillEditor content-type="html" theme="snow" class="form-control" id="address" name="address" v-model:content="vendorData.address" :class="{'is-invalid' : vendorData.errors.has('address') }"></QuillEditor>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor content-type="html" theme="snow" class="form-control" id="description" name="description" v-model:content="vendorData.description" :class="{'is-invalid' : vendorData.errors.has('description') }"></QuillEditor>
                </div>
            </div>
        </div>
        <input type="hidden" name="id" id="id" v-model="vendorData.id" />
        <button @click.prevent="editMode ? updateVendor() : createVendor()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </form>
</section>
</template>
<script>

export default {
    data(){
        return  {
            categories: [],
            loading: false,
            vendorData: new Form({
                address: '',
                category_id: "",
                description: '', 
                email: '', 
                id: '',
                name: '', 
                phone: '',
                tin: '',
                vatable: "",
                website: '',
                withholding_tax: '',
            }),
        }
    },
    emits: ['vendorReload'],
    mounted() {
        this.getInitials();
    },
    methods:{
        createVendor(){
            this.loading = true;
            this.vendorData.post('/api/procurement/vendors')
            .then(response =>{
                this.loading = false;
                this.$emit('vendorReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Vendor has been created',
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
            axios.get('/api/procurement/vendors/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Vendor Form did not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.categories = response.data.categories;
            //this.departments = response.data.departments;
        },
        updateVendor(){
            this.loading = true;
            this.vendorData.put('/api/procurement/vendors/'+this.vendorData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('vendorReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Vendor has been updated',
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
        editMode: Boolean,
        vendor: Object,
    },
    watch:{
        vendor(){
            this.vendorData.fill(this.vendor);
        }
    }
}
</script>