<template>
<section class="">
    <div class="row">
        <div class="col-md-4">
            <EMRLaboratoryDetailService :service.sync="service" @refreshServiceDetail="getAllInitials()"/>
        </div>
        <div class="col-md-8">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="analyte-tab" data-toggle="pill" href="#analyte" role="tab" aria-controls="analyte" aria-selected="true">Analytes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">Messages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-settings-tab" data-toggle="pill" href="#custom-tabs-one-settings" role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Settings</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <div class="tab-pane fade show active table-responsive p-0" id="analyte" role="tabpanel" aria-labelledby="analyte-tab">
                            <EMRLaboratoryDetailAnalyteList :analytes.sync="service.analytes" source="read"/>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                            Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam. 
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-messages" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
                            Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna. 
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-settings" role="tabpanel" aria-labelledby="custom-tabs-one-settings-tab">
                            Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis. 
                        </div>
                    </div>
                </div>
            </div>
        
            <!--EMRLaboratoryDetailReferenceRangeList :item.sync="item" :referral_ranges.sync="service.referral_ranges" source="service_detail"/-->
        </div>
    </div>
</section>
</template>
<script>
import EMRLaboratoryDetailReferenceRangeList from '@/emr/laboratory/details/ReferenceRangeList.vue';
import EMRLaboratoryDetailAnalyteList from '@/emr/laboratory/details/AnalyteList.vue';
import EMRLaboratoryDetailService from '@/emr/laboratory/details/Service.vue';

export default {
    components:{
        EMRLaboratoryDetailAnalyteList, EMRLaboratoryDetailService, 
    },    
    data() {
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