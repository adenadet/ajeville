<template>
    <section class="overlay-wrapper">
        <div class="row">
            <div class="col-md-6">
                <strong><i class="fas fa-money-bill mr-1"></i> Amount</strong>
                <p>{{ currency(payment.amount) }}</p>
                <hr>
                <strong><i class="fas fa-cash-register mr-1"></i> Payment Mode</strong>
                <p>{{ payment.mode != null ? payment.mode.name : 'Cash' }}</p>
                <hr>
                <strong><i class="fas fa-map-marker-alt mr-1"></i> Bank Account Details</strong>
                <p>{{ payment.account != null ? (payment.account.bank != null ? payment.account.account_name+' - '+payment.account.bank.bank_name : 'Deactivated Bank') + ' ['+payment.account.account_number+']'  : 'Cash'  }}</p>
                <hr>
                <strong><i class="fas fa-user-circle mr-1"></i> Collected</strong>
                <p>{{ FullName(payment.collector)}}</p>
                <span class="text-small text-muted">{{ ExcelDate(payment.collected_at) }}</span>
                <hr>
                <strong><i class="fas fa-check mr-1"></i> Status</strong>
                <p>{{ payment.status == 0 ? 'Unconfirmed' : 'Confirmed'}}</p>
                <hr>
                <div v-if="payment.status == 1">
                <strong><i class="fas fa-user-circle mr-1"></i> Confirmed By</strong>
                <p>{{ FullName(payment.confirmer)}} </p>
                <span class="text-small text-muted"><i class="fa fa-clock"></i>{{ ExcelDate(payment.confirmed_at) }}</span>
                <hr>
                </div>
                <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>
                <p v-html="payment.description"></p>
            </div>
            <div class="col-md-6">
                <div class="card card-tabs">
                    <div class="card-header bg-dark p-0 pt-1">
                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="allocation-tab" data-toggle="pill" href="#allocation" role="tab" aria-controls="allocation" aria-selected="true">Allocations</a>
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
                        <div class="tab-content" id="custom-tabs-one-tabContent" v-if="payment != null">
                            <div class="tab-pane fade show active" id="allocation" role="tabpanel" aria-labelledby="allocation-tab">
                                <FinanceDetailPaymentAllocationList :allocations.sync="payment.allocations != null ? payment.allocations : []" source="payment" />
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
            </div>
        </div>
        
    </section>
</template>
<script>
import SalesDetailOrderSummary from '@/sales_orders/details/OrderSummary.vue'; 
import FinanceDetailPaymentAllocationList from '@/finance/details/PaymentAllocationList.vue';
export default {
    components:{FinanceDetailPaymentAllocationList, SalesDetailOrderSummary},
    data(){
        return {
        }
    },
    emits:['paymentReload'],
    methods:{
        
    },
    mounted() {
        //this.getAllInitials();
    },
    props:{
        payment: Object,
    },
    watch:{
        
    },
}
</script>