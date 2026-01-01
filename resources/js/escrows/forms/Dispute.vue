<template>
    <form @submit.prevent="createDispute()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Transaction Detail</label>
                    <input class="form-control" type="text" name="title" id="title" v-model="disputeData.title">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Dispute Type</label>
                    <select class="form-control" name="type_id" id="type_id" v-model="disputeData.type_id">
                        <option value=""></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Complaint</label>
                    <QuillEditor class="form-control" v-model:content="disputeData.complaint" theme="snow" contentType="html"></QuillEditor>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-sm btn-primary" type="submit">Begin</button>
            </div>
        </div>
    </form>
</template>
<script>
import { QuillEditor } from '@vueup/vue-quill';

export default {
    computed:{
        all_vendors(){
            if (this.assignVendorData.category_id != '') {
                return this.vendors.filter(vendor => vendor.category_id === this.assignVendorData.category_id);
            }
            else{ return this.vendors}
        }
    },
    data() {
        return {
            categories: [],
            disputeData: new Form({
                date: '',
                title: '',
                request_id: '',
                buyer_id: '',
                buyer:{
                    name: '',
                    email: '',
                    phone: '',
                },
                seller_id: '',
                seller:{
                    name: '',
                    email: '',
                    phone: '',
                },
                broker_id: '',
                broker:{
                    name: '',
                    email: '',
                    phone: '',
                },
                product_id: '',
                product:{

                },
                invoice_id: '',
                item_details: '',
                details: '',
                item_type_id: '',
                amount: '',
                inspection_period: '',
                unique_code: '',
                confirmation_code: '',
                status: '',
                created_by: '',
                updated_by: '',
                completed_by: '',
            }),
            vendors: [],
            work_orders: [],
        }
    },
    emits:['refreshPage'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {        
        getAllInitials() {
            this.loading = true;
            axios.get('/api/escrows/transactions/initials')
            .then(response => {
                this.categories = response.data.categories;
                this.vendors = response.data.vendors;
                this.loading = false;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Vendor Assign Form did not loaded successfully',})
                this.loading = false;
            });
        },
        updateOtherCost(){
            this.loading = true;
            let address = this.item_type == 'purchase_order' ? '/api/procurement/purchase_orders/'+item.id : '/api/procurement/work_orders/'+item.id
            this.disputeData.put(address)
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Other Cost has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
    },
    props: {
        editMode: Boolean,
        item_type: String,
        item: Object,
    },
    watch:{
        item(){
            if (this.item_type == 'purchase_order'){this.disputeData.po_id = this.item.id;}
            else if (this.item_type == 'work_order'){this.disputeData.wo_id = this.item.id;}
        },
    }
}
</script>