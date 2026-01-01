<template>
    <section class="overlay-wrapper">
        <strong><i class="fas fa-money-bill mr-1"></i> Amount</strong>
        <p>{{ currency(pay_out.amount) }}</p>
        <hr>
        <strong><i class="fas fa-cash-register mr-1"></i> Payment Mode</strong>
        <p>{{ pay_out.mode != null ? pay_out.mode.name : 'Cash' }}</p>
        <hr>
        <strong><i class="fas fa-map-marker-alt mr-1"></i> Bank Account Details</strong>
        <p>{{ pay_out.account != null ? (pay_out.account.bank != null ? pay_out.account.account_name+' - '+pay_out.account.bank.bank_name : 'Deactivated Bank') + ' ['+pay_out.account.account_number+']'  : 'Cash'  }}</p>
        <hr>
        <strong><i class="fas fa-user-circle mr-1"></i> Collected</strong>
        <p>{{ FullName(pay_out.collector)}}</p>
        <span class="text-small text-muted">{{ ExcelDate(pay_out.collected_at) }}</span>
        <hr>
        <strong><i class="fas fa-check mr-1"></i> Status</strong>
        <p>{{ pay_out.status == 0 ? 'Unconfirmed' : 'Confirmed'}}</p>
        <hr>
        <div v-if="pay_out.status == 1">
        <strong><i class="fas fa-user-circle mr-1"></i> Confirmed By</strong>
        <p>{{ FullName(pay_out.confirmer)}} </p>
        <span class="text-small text-muted"><i class="fa fa-clock"></i>{{ ExcelDate(pay_out.confirmed_at) }}</span>
        <hr>
        </div>
        <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>
        <p v-html="pay_out.description"></p>
    </section>
</template>
<script>
export default {
    data(){
        return {
        }
    },
    emits:['pay_outReload'],
    methods:{
        
    },
    mounted() {
        //this.getAllInitials();
    },
    props:{
        pay_out: Object,
    },
    watch:{
        
    },
}
</script>