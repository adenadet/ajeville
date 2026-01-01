<template>
<section class="overlay-wrapper">
    <div class="table-responsive p-0">
        <table class="table table-striped table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Date</th>
                    <th v-if="source != 'pay_out'">Pay Out Mode</th>
                    <th v-if="source != 'expense'">Expense</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody v-if="allocations.length != 0">
                <tr v-for="(allocation, index) in allocations" :key="allocation.id" :class="allocation.status == 0 ? 'text-danger' : ''">
                    <td>{{ ExcelDate(allocation.date) }}</td>
                    <td v-if="source != 'pay_out'">{{ allocation.pay_out_id != null ? 'Payout: '+allocation.pay_out.unique_id : 'Wallet Payment'}}</td>
                    <td v-if="source != 'expense'">{{ allocation.expense.unique_id }}</td>
                    <td>{{ currency(allocation.amount) }}</td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="8">No PayOuts Created</td>
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