<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">Summary</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-four-messages-tab" data-toggle="pill" href="#custom-tabs-four-messages" role="tab" aria-controls="custom-tabs-four-messages" aria-selected="false">Timeline</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-four-settings-tab" data-toggle="pill" href="#custom-tabs-four-settings" role="tab" aria-controls="custom-tabs-four-settings" aria-selected="false">Settings</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                            <div class="card">
                                <div class="card-header bg-success"><h3 class="card-title">Transaction Summary</h3>
                                    <!--div class="card-tools">
                                        <button class="btn btn-tool"><i class="fa fa-edit"></i></button>
                                    </div-->
                                </div>
                                <div class="card-body">
                                    <strong><i class="fas fa-box mr-1"></i> Product</strong>
                                    <p class="text-muted">{{ transaction.title }}</p>
                                    <hr>

                                    <strong><i class="fas fa-user mr-1"></i> Buyer</strong>
                                    <p class="text-muted">{{ FullName(transaction.buyer) }}</p>
                                    <hr>

                                    <strong><i class="fas fa-user mr-1"></i> Seller</strong>
                                    <p class="text-muted">{{ FullName(transaction.seller) }}</p>
                                    <hr>

                                    <strong><i class="fas fa-pencil-alt mr-1"></i> Details</strong>
                                    <p class="text-muted" v-html="transaction.product != null ? transaction.product.details : 'Default Product'"></p>

                                    <hr>

                                    <strong><i class="far fa-file-alt mr-1"></i> Other Specifics</strong>
                                    <p class="text-muted">{{ transaction.product != null ? transaction.product.detailed : 'Default Product Specifics' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab">
                            <div class="card">
                                <div class="card-header bg-success"><h3 class="card-title">Contract</h3></div>
                                <div class="card-body p-0">
                                    <QuillEditor :read-only="false" contentType="html" v-model:content="transaction.contract"></QuillEditor>
                                </div>
                            </div>
                            <!--EscrowDetailContract :contract.sync="transaction.contract" /-->
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-four-messages" role="tabpanel" aria-labelledby="custom-tabs-four-messages-tab">
                            <EscrowDetailTransactionActivity :activities.sync="transaction.activities" />
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-four-settings" role="tabpanel" aria-labelledby="custom-tabs-four-settings-tab">
                            <!--Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis.--> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return  {
            current_page: 1,
            editMode: false,
            loading: false,
            transactions: { data: []},
        }
    },
    mounted() {
        //this.getAllInitials();
    },
    methods:{
        closeModals(){
            $('#transactionModal').modal('hide');
        },
        editTransaction(){
            this.loading = true;
            this.editMode = false;
            this.transaction = {};
            $('#transactionModal').modal('show');
            this.loading = false;  
        },
    },
    props:{
        activities: Array,
        transaction: Object,
    },
    watch:{
        transactions(){},
    }
}
</script>