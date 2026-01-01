<template>
<section>
    <form>
        <alert-error :form="customerData"></alert-error> 
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="customerData.name" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" id="category_id" name="category_id" v-model="customerData.category_id">
                        <option value="">--Select Category--</option>
                        <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Website</label>
                    <input type="text" class="form-control" name="website" id="website" v-model="customerData.website" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" id="email" v-model="customerData.email" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="number" class="form-control" name="phone"  id="phone" v-model="customerData.phone" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tax Identification Number (TIN)</label>
                    <input type="text" class="form-control" id="tin" name="tin" v-model="customerData.tin">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Withholding Tax</label>
                    <input type="text" class="form-control" name="withholding_tax" id="withholding_tax" v-model="customerData.withholding_tax" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Is Service VATable?</label>
                    <select class="form-control" name="vatable"  id="vatable" v-model="customerData.vatable">
                        <option value="">--Select If VAT applies to Customer--</option>
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
                    <QuillEditor content-type="html" theme="snow" class="form-control" id="address" name="address" v-model:content="customerData.address" :class="{'is-invalid' : customerData.errors.has('address') }"></QuillEditor>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Delivery Address</label>
                    <QuillEditor content-type="html" theme="snow" class="form-control" id="delivery_address" name="delivery_address" v-model:content="customerData.delivery_address" :class="{'is-invalid' : customerData.errors.has('delivery_address') }"></QuillEditor>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor content-type="html" theme="snow" class="form-control" id="description" name="description" v-model:content="customerData.description" :class="{'is-invalid' : customerData.errors.has('description') }"></QuillEditor>
                </div>
            </div>
        </div>
        <input type="hidden" name="id" id="id" v-model="customerData.id" />
        <button @click.prevent="editMode ? updateCustomer() : createCustomer()" type="submit" name="submit" class="submit btn btn-primary">Submit</button>
    </form>
</section>
</template>
<script>

export default {
    data(){
        return  {
            categories: [],
            loading: false,
            customerData: new Form({
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
    emits: ['customerReload'],
    mounted() {
        this.getInitials();
    },
    methods:{
        createCustomer(){
            this.loading = true;
            this.customerData.post('/api/sales/customers')
            .then(response =>{
                this.loading = false;
                this.$emit('customerReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Customer has been created',
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
            axios.get('/api/sales/customers/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Customer Form did not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.categories = response.data.categories;
            //this.departments = response.data.departments;
        },
        updateCustomer(){
            this.loading = true;
            this.customerData.put('/api/sales/customers/'+this.customerData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('customerReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Customer has been updated',
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
        customer: Object,
    },
    watch:{
        customer(){
            this.customerData.fill(this.customer);
        }
    }
}
</script>