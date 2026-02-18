<template>
<section class="">
    <div class="row">
        <div class="col-md-6">
            <EMRLaboratoryDetailService :service.sync="service" @refreshServiceDetail="getAllInitials()"/>
        </div>
        <div class="col-md-6">
            <EMRLaboratoryDetailReferenceRangeList :item.sync="item" :referral_ranges.sync="service.referral_ranges" source="service_detail"/>
        </div>
    </div>
</section>
</template>
<script>
import EMRLaboratoryDetailReferenceRangeList from '@/emr/laboratory/details/ReferenceRangeList.vue';
import EMRLaboratoryDetailService from '@/emr/laboratory/details/Service.vue';

export default {
    components:{EMRLaboratoryDetailService},    data() {
        return {
            loading: false,
            item: {},
            service: {referral_ranges: [],},
        }
    },
    mounted() {
        this.getAllInitials();
        /*Fire.$on('visitUpdated', () =>{
            this.closeModal();
        });*/
    },
    methods: {
        closeModal(){
            $('#paymentModal').modal('hide');
        },
        getAllInitials(page=1) {
            this.loading = true;
            axios.get('/api/emr/laboratory/services/'+this.$route.params.id+'?page='+page).then(response => {
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Visit Form was not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.service = response.data.service;
            this.item = response.data.service?.service?.item ?? [];
        },
                
    },
}
</script>