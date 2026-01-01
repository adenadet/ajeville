<template>
<section class="overlay-wrapper p-0">
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Date</th>
                <th>Status</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
        <tr>
            <td>183</td>
            <td>John Doe</td>
            <td>11-7-2014</td>
            <td><span class="tag tag-success">Approved</span></td>
            <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
        </tr>
        <tr>
            <td>219</td>
            <td>Alexander Pierce</td>
            <td>11-7-2014</td>
            <td><span class="tag tag-warning">Pending</span></td>
            <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
        </tr>
        <tr>
            <td>657</td>
            <td>Bob Doe</td>
            <td>11-7-2014</td>
            <td><span class="tag tag-primary">Approved</span></td>
            <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
        </tr>
        <tr>
            <td>175</td>
            <td>Mike Doe</td>
            <td>11-7-2014</td>
            <td><span class="tag tag-danger">Denied</span></td>
            <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
        </tr>
        <tr>
            <td>134</td>
            <td>Jim Doe</td>
            <td>11-7-2014</td>
            <td><span class="tag tag-success">Approved</span></td>
            <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
        </tr>
        <tr>
            <td>494</td>
            <td>Victoria Doe</td>
            <td>11-7-2014</td>
            <td><span class="tag tag-warning">Pending</span></td>
            <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
        </tr>
        <tr>
            <td>832</td>
            <td>Michael Doe</td>
            <td>11-7-2014</td>
            <td><span class="tag tag-primary">Approved</span></td>
            <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
        </tr>
        <tr>
            <td>982</td>
            <td>Rocky Doe</td>
            <td>11-7-2014</td>
            <td><span class="tag tag-danger">Denied</span></td>
            <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
        </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    computed:{
        patient(){
            var patient = this.$store.getters.currentPatient;
            return patient;
        },
        visit(){
            var visit = this.$store.getters.currentVisit;
            return visit;
        },
        transactions(){
            if(this.source == 'visit'){return  this.visit.transactions;}
            else{return this.patient.transactions;}
        },
    },
    data() {
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            patient_id: '',
        }
    },
    mounted() {},
    methods: {
        deactivateAccountChart(chart_account){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This Chart of Account would be deactivated and not available for assignment",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/finance/chart_accounts/'+transaction.id)
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
        closeModal(){
            $('#paymentModal').modal('hide');  
            $('#serviceModal').modal('hide');  
            $('#transactionModal').modal('hide');  
        },
        viaWallet(transaction){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "The patient's wallet would be debited for this transaction",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }) 
            .then((result) => {
                if(result.value){
                    this.form.transaction_id = transaction.id;
                    this.form.post('/api/finance/payments')
                    .then(response=>{
                        Swal.fire('Update!', response.data.message, response.data.icon);
                        //this.getInitials();  
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        viewTransaction(transaction){
            this.transaction = transaction;
            Fire.$emit('viewTransaction', transaction);
            $('#transactionModal').modal('show');
        },
    },
    props:{
        source: String,
    }
}
</script>