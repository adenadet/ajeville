<template>
<section class="overlay-wrapper p-0">
    <div class="card card-outline card-primary">
        <div class="card-header bg"><h3 class="card-title">Summary</h3></div>
        <div class="card-body">
            <strong><i class="fas fa-calendar mr-1"></i> ID:</strong>
            <p class="text-muted">{{ income.unique_id }}</p>
            <hr>
            <strong><i class="fas fa-calendar mr-1"></i> Date | Due Date</strong>
            <p class="text-muted">{{ income.date }} | {{ income.due_date }}</p>
            <hr>
            <strong><i class="fas fa-money-bill mr-1"></i> Total Amount</strong>
            <p class="text-muted">{{ income.amount != null ? currency(income.amount) : '0.00' }}</p>
            <hr>
            <strong><i class="fas fa-calendar-check mr-1"></i> Status</strong><br />
                <span v-if="income.status==1" class="badge badge-warning">Unconfirmed</span>
                <span v-else-if="income.status==5" class="badge bg-orange">Queried</span>
                <span v-else-if="income.status==10" class="badge badge-info">Confirmed</span>
                <span v-else-if="income.status==40" class="badge badge-danger">Rejected</span>
                <span v-else-if="income.status==100" class="badge badge-danger">Deleted</span>
                <span v-else-if="income.status==10" class="badge bg-success">Paid</span>
                <span v-else-if="income.status==300" class="badge bg-warning">Part Paid</span>
            <hr>
            <strong><i class="fas fa-map-marker-alt mr-1"></i> Income Type</strong>
            <p class="text-muted">{{ className(income.incomeable_type) == 'Order' ? 'Sales Order' : 'Other Income'}}</p>
            <hr>
            <strong><i class="fas fa-pencil-alt mr-1"></i> Details:</strong>
            <br /><a v-if="income.incomeable != null" :href="`/sales_orders/orders/${income.incomeable.unique_id}`">{{ income.incomeable != null ? income.incomeable.unique_id : '' }}</a>
            <hr>
            <strong><i class="far fa-file-alt mr-1"></i> Description:</strong>
            <p class="text-muted" v-html="income.description"></p>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            loading: false,
        }
    },
    methods: {
    },
    props:{
        income: Object,
        source: String,
    }
}
</script>