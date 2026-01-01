<template>
<section class="overlay-wrapper p-0">
    <div class="card card-outline card-primary">
        <div class="card-header bg"><h3 class="card-title">Summary</h3></div>
        <!-- /.card-header -->
        <div class="card-body">
            <strong><i class="fas fa-calendar mr-1"></i> ID:</strong>
            <p class="text-muted">{{ expense.unique_id }}</p>
            <hr>
            <strong><i class="fas fa-calendar mr-1"></i> Date</strong>
            <p class="text-muted">{{ expense.date }}</p>
            <hr>
            <strong><i class="fas fa-calendar-check mr-1"></i> Due Date</strong>
            <p class="text-muted">{{ expense.due_date }}
                <span><i class="fa fa-check"></i></span>
            </p>
            <hr>
            <strong><i class="fas fa-calendar-check mr-1"></i> Status</strong><br />
                <span v-if="expense.status==1" class="badge badge-warning">Unconfirmed</span>
                <span v-else-if="expense.status==5" class="badge bg-orange">Queried</span>
                <span v-else-if="expense.status==10" class="badge badge-info">Confirmed</span>
                <span v-else-if="expense.status==40" class="badge badge-danger">Rejected</span>
                <span v-else-if="expense.status==100" class="badge badge-danger">Deleted</span>
                <span v-else-if="expense.status==10" class="badge bg-success">Paid</span>
                {{ expense.status }}
            <hr>
            <strong><i class="fas fa-map-marker-alt mr-1"></i> Expense Type</strong>
            <p class="text-muted">{{ expense.expense_type != null ? expense.expense_type.name : 'No Expense Type Assigned'}}</p>
            <hr>
            <strong><i class="fas fa-pencil-alt mr-1"></i> Details:</strong>
            <p class="text-muted" v-if="expense.expense_classification == 'Vendor Payment'">
                Paid to: {{ expense.vendor != null ? expense.vendor.name : 'Not yet assigned' }}
            </p>
            <p class="text-muted" v-else-if="expense.expense_classification == 'Customer Refund'">
                Paid to:  {{ expense.customer != null ? expense.customer.name : 'Not yet assigned' }}
            </p>
            <p class="text-muted" v-else-if="expense.expense_classification == 'Customer Refund'">
                Paid to:  {{ expense.staff != null ? FullName(expense.staff) : 'Not yet assigned' }}
            </p>
            <hr>
            <strong><i class="far fa-file-alt mr-1"></i> Description:</strong>
            <p class="text-muted" v-html="expense.description"></p>
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
        expense: Object,
        source: String,
    }
}
</script>