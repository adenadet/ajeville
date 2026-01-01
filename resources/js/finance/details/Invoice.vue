<template>
<section class="overlay-wrapper p-0">
    <strong><i class="fas fa-map-marker-alt mr-1"></i> Unique ID | Invoice ID</strong>
    <p class="text-muted">{{ invoice.unique_id }} | {{ invoice.invoice_number }}</p>
    <hr>
    <strong><i class="fas fa-user-circle mr-1"></i> Vendor</strong>
    <p class="text-muted">{{ invoice.vendor != null ? invoice.vendor.name : 'Other Vendors'}}</p>
    <hr>
    <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>
    <p class="text-muted">{{ invoice.branch != null ? invoice.branch.name : 'No branch assigned' }}</p>
    <hr>
    <strong><i class="fas fa-calendar mr-1"></i> Date</strong>
    <p class="text-muted">{{ ExcelDate(invoice.date) }}</p>
    <hr>
    <strong><i class="fas fa-calendar-check mr-1"></i>Due Date</strong>
    <p class="text-muted">{{ ExcelDate(invoice.due_date) }}</p>
    <hr>
    <strong><i class="fas fa-money-bill mr-1"></i>Amount</strong>
    <p class="text-muted">{{ currency(invoice.amount) }}</p>
    <hr>
    <strong><i class="fas fa-pencil-alt mr-1"></i> Status</strong>
    <p class="text-muted">
        <span v-if="invoice.status == 1" class="badge badge-warning">Pending Confirmation</span>
        <span v-else-if="invoice.status == 2" class="badge badge-primary">Confirmed</span>
        <span v-else-if="invoice.status == 5" class="badge badge-dark">Ongoing</span>
        <span v-else-if="invoice.status == 10" class="badge badge-success">Completed</span>
        <span v-else class="badge badge-danger">Queried</span>
    </p>
    <hr>
    <strong><i class="far fa-file-alt mr-1"></i> Description</strong>
    <p class="text-muted" v-html="invoice.description"></p>
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
    emits: ['refreshInvoiceList'],
    mounted() {},
    methods: {},
    props: {
        invoice: Object,
    }
}
</script>