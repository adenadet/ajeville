<template>
<section class="overlay-wrapper">
    <div v-if="loading" class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="paymentTermFormModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{ editMode ? 'Update Payment Term: '+ payment_term.name : 'New Payment Term' }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ProcurementFormPaymentTerm :payment_term.sync="payment_term" :editMode.sync="editMode" @vendorPaymentTermReload="getInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Vendor Payment Terms</h3>
            <div class="card-tools">
                <button class="btn btn-sm btn-primary" @click="addPaymentTerm()">Add Payment Term</button>
            </div>
        </div>
        <div class="card-body table-responsive p-0" style="height: 500px;">
            <table class="table table-head-fixed text-nowrap">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Payment Term Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(payment_term, index) in payment_terms.data" :key="index">
                        <td>{{ payment_term.id }}</td>
                        <td>{{ payment_term.name }}</td>
                        <td v-html="readMore(payment_term.description, 25, '...')"></td>
                        <td>{{ payment_term.status == 1 ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm" @click="editPaymentTerm(payment_term)">Edit</button>
                            <button class="btn btn-danger btn-sm" @click="deletePaymentTerm(payment_term)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getInitials" :per-page="payment_terms.per_page != null ? payment_terms.per_page : 52" :records="payment_terms.total != null ? payment_terms.total : 550" ></pagination>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            payment_term: {},
            payment_terms: {},
            current_page: 1,
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        addPaymentTerm(){
            this.loading = true;
            this.editMode = false;
            this.payment_term = {};
            $('#paymentTermFormModal').modal('show');  
            this.loading = false;
        },
        editPaymentTerm(payment_term){
            this.loading = true;
            this.editMode = true;
            this.payment_term = payment_term;
            $('#paymentTermFormModal').modal('show');  
            this.loading = false;
        },
        closeModals(){
            $('#paymentTermFormModal').modal('hide');
        },
        deletePaymentTerm(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                })
            .then((result) => {
                if(result.value){
                    this.form.delete('/api/procurement/payment_terms/'+id)
                    .then(response=>{
                        this.$emit('vendorPaymentTermReload', response);  
                        this.$swal.fire('Deleted!', 'Vendor PaymentTerm has been deleted.', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials(page=1){
            this.closeModals();
            this.loading = true;
            axios.get('/api/procurement/payment_terms?page='+page+'&type='+this.type+'&search='+this.search)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Vendor PaymentTerms not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.payment_terms = response.data.payment_terms;
        },
        updateVendorPaymentTerm(vendor){
            this.loading = true;
            this.editMode = true;
            this.vendor = vendor;
            $('#vendorPaymentTermFormModal').modal('show');
            this.loading = false;         
        },
    },
}
</script>