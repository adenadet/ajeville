<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="paymentModeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Payment Mode Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <FinanceDetailPaymentMode :payment_mode="payment_mode" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="paymentModeFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Large Modal</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <FinanceFormPaymentMode :payment_mode="payment_mode" :editMode="editMode"  @refreshPaymentMode="getInitials" />
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>Last Updated By</th>
                <th>Last Updated At</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="payment_modes.length > 0">
            <tr v-for="(payment_mode, index) in payment_modes" :key="payment_mode.id">
                <td>{{ addOne(index) }}</td>
                <td>{{ payment_mode.name }}</td>
                <td v-html="readMore(payment_mode.description, 50, '...')"></td>
                <td>{{ payment_mode.status == 1 ? 'Active' : 'Deactivated' }}</td>
                <td>{{ payment_mode.creator ? FullName(payment_mode.creator) : 'System Generated'}}</td>
                <td>{{ ExcelDate(payment_mode.created_at) }}</td>
                <td>{{ payment_mode.updater ? FullName(payment_mode.updater) : 'No Bank Assigned'}}</td>
                <td>{{ ExcelDate(payment_mode.updated_at) }}</td>
                <td>
                    <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu">
                        <button class="btn btn-block dropdown-item" @click="viewPaymentMode(payment_mode)"><i class="fa fa-eye mr-1 text-primary"></i> View Account </button>
                        <button class="btn btn-block dropdown-item" @click="editPaymentMode(payment_mode)"><i class="fa fa-edit mr-1 text-warning"></i> Edit Account </button>
                        <button class="btn btn-block dropdown-item" @click="deactivatePaymentMode(payment_mode.id)"><i class="fa fa-trash mr-1 text-danger"></i> {{payment_mode.status == 1 ? 'Deactivate' : 'Reactivate'}} Account </button>
                    
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="5">No Payment Mode meets your requirement</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            payment_mode: {},
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    mounted() {},
    methods: {
        closeModal(){
            $('#paymentModeModal').modal('hide');  
            $('#paymentModeFormModal').modal('hide');  
 
        },
        deactivatePaymentMode(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Branch Account would be deactivated and not available for assignment",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/finance/payment_modes/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', response.data.message, response.data.icon);
                        this.loading = false; 
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editPaymentMode(payment_mode){
            this.loading = true;
            this.editMode = true;
            this.payment_mode = payment_mode;
            $('#paymentModeFormModal').modal('show');
            this.loading = false;  
        },
        viewPaymentMode(payment_mode){
            this.payment_mode = payment_mode;
            $('#paymentModeModal').modal('show');
        },
    },
    props:{
        source: String,
        payment_modes: {type: Array, default: () => [],}
    }
}
</script>