<template>
<section>
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">My Disputes</h3>
            </div>
            <div class="card-body table-responsive p-0" style="height: 600px;">
                <EscrowDetailDisputeList :disputes="disputes.data" source="admin" />
            </div>
            <div class="card-footer">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="disputes.per_page != null ? disputes.per_page : 52" :records="disputes.total != null ? disputes.total : 550" >
                </pagination>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            current_page: 1,
            editMode: false,
            loading: false,
            disputes: { data: []},
            dispute: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        addTransaction(){
            this.loading = true;
            this.editMode = false;
            this.product = {};
            $('#productModal').modal('show');
            this.loading = false;  
        },
        closeModals(){
            $('#disputeModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/escrows/disputes?t=admin&page='+this.current_page)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Disputes loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Disputes not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.partners = response.data.partners;
            this.closeModals();
        },
    },
}
</script>