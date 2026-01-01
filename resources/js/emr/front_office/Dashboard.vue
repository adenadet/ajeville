<template>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner"><h3>150</h3><p>All Patients</p></div>
                    <div class="icon"><i class="fa fa-users"></i></div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner"><h3>53<sup style="font-size: 20px">%</sup></h3><p>Bounce Rate</p></div>
                    <div class="icon"><i class="fa fa-user-plus"></i></div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner"><h3>44</h3><p>Temporary Patient</p></div>
                    <div class="icon"><i class="fa fa-user-circle"></i></div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary ">
                    <div class="inner"><h3>65</h3><p>Active Patients</p></div>
                    <div class="icon"><i class="fa fa-user-tag"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-12">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">Active Visits</h3>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 400px;">
                        <EMRVisitationsDetailsList :visits="visits" view="dashboard" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
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
        this.getAllInitials();
    },
    methods: {
        enterAuthCode(){
            $('#authCodeModal').modal('show');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/emr/hims/dashboard').then(response =>{
                this.refresh(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
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
        transactions: Array,
        view: String,
    },
}
</script>