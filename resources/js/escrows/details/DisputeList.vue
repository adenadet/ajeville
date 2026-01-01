<template>
    <div class="card-body table-responsive p-0" style="height: 300px;">
        <table class="table table-head-fixed text-nowrap">
            <thead>
                <tr>
                    <th>Dispute ID</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Partner</th>
                    <th>Transaction ID</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="disputes.length > 0">
                <tr v-for="dispute in disputes">
                    <td>183</td>
                    <td>183</td>
                    <td>John Doe</td>
                    <td>11-7-2014</td>
                    <td><span class="tag tag-success">Approved</span></td>
                    <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr><td colspan="7">No Dispute has been created</td></tr>
            </tbody>
        </table>
    </div>
</template>
<script>
export default {
    data(){
        return {
            editMode: false,
            loading: false,
            dispute: {},
            style: 'table',
        }
    },
    methods:{
        adddispute(){
            this.loading = true;
            this.editMode = false;
            this.dispute = {};
            $('#disputeModal').modal('show');
            this.loading = false; 
        },
        deactivatedispute(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This dispute will no longer be available to people who visit your page",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, deactivate it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/escrows/disputes/'+id)
                    .then(response=>{
                        this.$swal.fire('Deactivated!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        startTransaction(dispute){
            this.loading = true;
            this.editMode = false;
            this.dispute = dispute;
            $('#transactionModal').modal('show');
            this.loading = false;
        },
        switchStyle(text){
            this.style = text;
        },
        updatedispute(dispute){
            alert(dispute.details);
            this.loading = true;
            this.editMode = true;
            this.dispute = product;
            $('#productModal').modal('show');
            this.loading = false;
        }
    },
    mounted() {},
    props:{
        disputes: Array,
        source: String,
    },
    watch:{}
}
</script>