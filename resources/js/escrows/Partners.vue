<template>
<section>
    <div class="col-12">
        <div class="card">
            <EscrowDetailPartnerList :partners="partners.data" source="main" />
            <div class="card-footer">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="partners.per_page != null ? partners.per_page : 52" :records="partners.total != null ? partners.total : 550"></pagination>
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
            partners: { data: []},
            partner: {},
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
            $('#partnerModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/escrows/partners?t=my&page='+this.current_page)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Partners loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Partners not loaded successfully',
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