<template>
    <div class="card-body table-responsive p-0" style="height: 300px;">
        <table class="table table-head-fixed text-nowrap">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Visit ID</th>
                    <th>Patient </th>
                    <th>Visit Type</th>
                    <th>Consultant</th>
                    <th>Consulting Group</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="visit in visits" :key="visit.id">
                    <td>183</td>
                    <td>John Doe</td>
                    <td>11-7-2014</td>
                    <td><span class="tag tag-success">Approved</span></td>
                    <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            loading: true,
            selected_transactions: [],
            //transactions: {},
            transaction: {}, 
            transaction_list: [],
        }
    },
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        enterAuthCode(){
            $('#authCodeModal').modal('show');
        },
        closeModal(){
            $('#authCodeModal').modal('hide');
            $('#requestCodeModal').modal('hide');
            $('#planModal').modal('hide');
            $('#providerModal').modal('hide');
        },
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/emr/insurance/transactions?q=auth').then(response =>{
                this.refresh(response);
                this.$Progress.finish();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Visits were not loaded successfully',
                })
            });
        },
        inputAuthCode(){

        },
        inputAuthCodes(){
            if (this.selected_transactions.length == 0){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'No items selected!',
                    footer: 'Please select at least one item!'
                });
            }
            else{
                this.transaction_list = this.selected_transactions;
                $('#authCodeModal').modal('show');
            }
        },
        refresh(response){
            this.transactions = response.data.transactions;
            this.transaction = response.data.transactions.data[0];
            this.loading = false;
        },
        rejectTransaction(transaction){},
        requestCode(transaction){
            this.editMode = false;
            Fire.$emit('providerDataFill', {});
            $('#requestCodeModal').modal('show');
        },
        setCoverage(transaction){},
    },
    props:{
        view: String,
        visits: Array,
    },
}
</script>