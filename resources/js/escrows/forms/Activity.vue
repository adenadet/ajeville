<template>
<section>
    <form role="form" @submit.prevent="acceptEscrow">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Transaction</label>
                    <div type="text" class="form-control">{{ transaction.unique_code }}</div>
                    <input type="hidden" name="transaction_id" id="transaction_id" v-model="acceptanceData.transaction_id" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Decision</label>
                    <select class="form-control" v-model="acceptanceData.decision" name="decision" id="decision">
                        <option value="">--Select Decision--</option>
                        <option value="accept">Accept</option>
                        <option value="reject">Reject</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Details</label>
                    <QuillEditor v-model:content="acceptanceData.details" name="details" id="details"></QuillEditor>
                </div>
            </div>
        </div> 
        <div class="row">
            <div class="col-sm-12"><input type="submit" name="submit" class="submit btn btn-success" value="Submit" /></div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            acceptanceData: new Form({
                'transaction_id': '',
                'decision': '',
                'details': '', 
            }),
            loading: false,
        }
    },
    emits:['reload'],
    mounted(){},
    methods:{
        acceptEscrow(){
            this.loading = true
            this.acceptanceData.put('/api/escrows/transactions/accept/'.this.transaction.id)
            .then(response =>{
                this.loading = false
                this.$emit('reload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Transaction "'+ this.transaction.unique_code+'" has been '+(this.acceptanceData.action == 'accept' ? 'accepted' : 'rejected'),
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            });
        },
    },
    props:{
        'transaction': Object,
    },
    watch:{
        transaction(){
            this.acceptanceData.id = this.transaction.id;
        }
    }
}
</script>