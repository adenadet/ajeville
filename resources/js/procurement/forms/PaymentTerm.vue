<template>
<section>
    <form @submit.prevent="editMode ? updatePaymentTerms() : createPaymentTerms()">
        <alert-error :form="paymentTermData"></alert-error> 
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="vendor_id">Name:</label>
                    <input type="text" name="name" id="name" class="form-control" v-model="paymentTermData.name" placeholder="Name" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select class="form-control" v-model="paymentTermData.status" id="status">
                        <option value="">--Select Status--</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="status">Description:</label>
                    <QuillEditor class="form-control"content-type="html" v-model:content="paymentTermData.description" id="description" name="description" placeholder="Description" />
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
            paymentTermData: new Form({
                description: '',
                id: '',
                name: '',
                status: '',
            }),
            loading: false,
        };
    },
    emits:['paymentTermsReload'],
    methods: {
        createPaymentTerms() {
            this.loading = true;
            this.paymentTermData.post('/api/procurement/payment_terms')
            .then(response =>{
                this.loading = false;
                this.$emit('paymentTermsReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Vendor PaymentTerms has been created',
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
        updatePaymentTerms(){
            this.loading = true;
            this.paymentTermData.put('/api/procurement/payment_terms/'+this.paymentTermData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('paymentTermsReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Vendor PaymentTerms has been updated',
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
        payment_term: Object,
        editMode: Boolean,
    },
    watch:{
        payment_term(){
            this.paymentTermData.reset();
            if ((this.payment_term != null) && (this.payment_term.id != null)){
                this.paymentTermData.fill(this.category);
            }
        }
    }
};
</script>