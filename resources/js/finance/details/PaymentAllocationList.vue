<template>
<section class="overlay-wrapper">
    <div class="table-responsive p-0">
        <table class="table table-striped table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Date</th>
                    <th v-if="source != 'payment'">Payment Mode</th>
                    <th v-if="source != 'income'">Income</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody v-if="allocations.length != 0">
                <tr v-for="(allocation, index) in allocations" :key="allocation.id" :class="allocation.status == 0 ? 'text-danger' : ''">
                    <td>{{ allocation.date }}</td>
                    <td v-if="source != 'payment'">{{ allocation.payment_id != null ? 'Payout: '+allocation.payment.unique_id : 'Wallet Payment'}}</td>
                    <td v-if="source != 'income'">{{ allocation.income.unique_id }}</td>
                    <td>{{ currency(allocation.amount) }}</td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="8">No Payments Created</td>
                </tr>
            </tbody>
        </table>    
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            payment: {},
        }
    },
    emits:['refreshAllocationList'],
    mounted() {},
    methods: {},
    props:{
        allocations: Array,
        source: String,
    }
}
</script>