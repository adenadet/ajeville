<template>
    <section class="row">
        <div class="col-md-12">
            <div class="position-relative">
                <div class="ribbon-wrapper ribbon-xl">
                    <div class="ribbon bg-danger">
                        {{ request.status == 0 ? 'Unpaid' : 'Correct Status'}}
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        Laboratory Request Summary 
                    </div>
                    <div class="card-body table-responsive p-0">                
                        <table class="table table-bordered table-hover table-stripped">
                            <tbody>
                                <tr>
                                    <td>Patient</td>
                                    <td>{{ request.patient | patientName}}</td>
                                </tr>
                                <tr>
                                    <td>Balance</td>
                                    <td>{{ request.patient.balance | currency}}</td>
                                </tr>
                                <tr>
                                    <td>Item</td>
                                    <td>{{ request.item.name}}</td>
                                </tr>
                                <tr v-if="request.status >= 2">
                                    <td>Unique ID</td>
                                    <td>{{ request.unique_id}}</td>
                                </tr>
                                <tr>
                                    <td>Category</td>
                                    <td>{{ request.item.category != null ? request.item.category.name : 'No Category Yet' }}</td>
                                </tr>
                                <tr>
                                    <td>Requested By</td>
                                    <td>{{ request.creator | FullName }}</td>
                                </tr>
                                <tr>
                                    <td>Requested At</td>
                                    <td>{{ request.created_at | excelDate }}</td>
                                </tr>
                                <tr v-if="request.status >= 2">
                                    <td>Collected By</td>
                                    <td>{{ request.creator | FullName }}</td>
                                </tr>
                                <tr v-if="request.status >= 2">
                                    <td>Collected At</td>
                                    <td>{{ request.created_at | excelDate }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer p-0" v-show="show_status">
                        <div class="float-right btn-group">
                            <button v-if="request.status == 0" class="btn btn-default float-right m-2 mr-0"><i class="fa fa-cash-register text-success mr-1"></i>Receive Payment</button>
                            <router-link v-if="request.status == 1 || request.special != null" class="btn btn-default float-right m-2 mr-0" :to="'/laboratory/requests/'+request.id+'/collect'" ><i class="fa fa-vial text-success mr-1"></i>Collect Sample</router-link>
                            <button v-else-if="request.status == 2" class="btn btn-default float-right m-2 mr-0"><i class="fa fa-vial text-success mr-1"></i>Enter Result</button>
                            <button v-else-if="request.status == 3 || request.status == 4 " class="btn btn-default float-right m-2 mr-0"><i class="fa fa-vial text-success mr-1"></i>Approve Result</button>
                            <button v-else-if="request.status == 3" class="btn btn-default float-right m-2 mr-0"><i class="fa fa-vial text-success mr-1"></i>Seek Secondary Result</button>
                        </div>
                    </div>
                    <div class="card-footer p-0" v-show="print_label">
                        <router-link class="btn btn-default bg-dark float-right m-2 mr-0" :to="'/laboratory/requests/'+request.id+'/print'" ><i class="fa fa-print mr-1"></i>Print Label</router-link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {}
    },
    mounted() {
    },
    methods: {
        
    },
    props: {
        request: Object,
        print_label: Boolean,
        show_status: Boolean,
    }
}
</script>