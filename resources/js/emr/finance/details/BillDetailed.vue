<template>
<section class="overlay-wrapper p-0">
    <div class="invoice p-3 mb-3" id="detailed-bill">
        <h4>
            <i class="fas fa-file-invoice"></i> Detailed Visit Bill
        </h4>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Service Type</th>
                            <th>Service</th>
                            <th>Qty</th>
                            <th>Unit Cost</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in detailedTransactions" :key="item.id">
                            <td>{{ formatDate(item.date) }}</td>
                            <td>{{ item.service_type_name }}</td>
                            <td>{{ item.service_name }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ currency(item.unit_cost) }}</td>
                            <td>{{ currency(item.total) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
</template>