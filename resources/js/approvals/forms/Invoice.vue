<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Reference ID</label>
                <div class="form-control">
                    {{ invoice.unique_id }}
                </div>
            </div>
        </div> 
        <div class="col-md-12">
            <div class="form-group">
                <label>Decision</label>
                <select class="form-control" id="action" name="action" v-model="approvalData.action">
                    <option value="">--Select Decision--</option>
                    <option value="confirm">Confirm</option>
                    <option value="reject">Reject</option>
                </select>
            </div>
            <div class="form-group">
                <label>Remark</label>
                <QuillEditor class="form-control" contentType="html" name="description" id="description" v-model.content="approvalData.description" placeholder="Description"></QuillEditor>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 float-right">
            <button class="btn btn-primary" @click="approveRequest">Submit</button>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            approvalData: new Form({
                action: '',
                invoice: {},
                description: '',
            }),
            editMode: false,
            form_type: '',
            loading: false, 
        }
    },
    mounted() {},
    emits: ['approvalInvoiceReload'],
    methods:{
        approveRequest(){
            this.loading = true;
            this.approvalData.put('/api/finance/invoices/'+this.approvalData.invoice.id+'/approve')
            .then(response =>{
                this.loading = false;
                this.$emit('approvalInvoiceReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Invoice has been approved',
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
            this.loading = false;
        },
    },
    props: {
        invoice: Object,
        invoice_type: String,
    },
    watch:{
        invoice(){
            this.loading = true;
            this.approvalData.invoice = this.invoice;
            this.loading = false;
        }
    }
}
</script>