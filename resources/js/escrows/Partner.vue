<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">User Detail</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1">
                    <div class="row">
                        <div class="col-12 col-sm-4">
                            <div class="card m-b-30">
                                <div class="card-body">
                                    <div class="media">
                                        <span class="align-self-center mr-3 action-icon badge badge-secondary-inverse"><i class="fa fa-star"></i></span>
                                        <div class="media-body">
                                            <p class="mb-0">Review Rating</p>
                                            <h5 class="mb-0">{{ stars(partner.review_avg) }}</h5>                      
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="card m-b-30">
                                <div class="card-body">
                                    <div class="media">
                                        <span class="align-self-center mr-3 action-icon badge badge-secondary-inverse"><i class="fa fa-tags text-bold mr-1"></i></span>
                                        <div class="media-body">
                                            <p class="mb-0">Total Transactions</p>
                                            <h5 class="mb-0">{{ transaction_count }}</h5>                      
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="card m-b-30">
                                <div class="card-body">
                                    <div class="media">
                                        <span class="align-self-center mr-3 action-icon badge badge-secondary-inverse"><i class="fa fa-tags text-bold mr-1"></i></span>
                                        <div class="media-body">
                                            <p class="mb-0">Total Projects</p>
                                            <h5 class="mb-0">{{ product_count }}</h5>                      
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-sm-6">
                            <div class="card card-outline card-outline-tabs">
                                <div class="card-header p-0 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">Products</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">Reviews</a>
                                        </li>
                                        <!--li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-four-messages-tab" data-toggle="pill" href="#custom-tabs-four-messages" role="tab" aria-controls="custom-tabs-four-messages" aria-selected="false">Messages</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-four-settings-tab" data-toggle="pill" href="#custom-tabs-four-settings" role="tab" aria-controls="custom-tabs-four-settings" aria-selected="false">Settings</a>
                                        </li-->
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                                            <EscrowDetailProductList  :products="products" source="browse"/>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab">
                                            <EscrowDetailReviewList :reviews="reviews" source="browse"/>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-four-messages" role="tabpanel" aria-labelledby="custom-tabs-four-messages-tab"></div>
                                        <div class="tab-pane fade" id="custom-tabs-four-settings" role="tabpanel" aria-labelledby="custom-tabs-four-settings-tab"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-4 order-1 order-md-2">
                    <img :src="'/img/profile/'+partner.image" alt="user" class="img-fluid mb-3">
                    <h3 class="text-primary"><i class="fas fa-paint-brush"></i> {{FullName(partner)}}</h3>
                    <p class="text-muted"></p>
                    <br>
                    <div class="text-muted">
                        <p class="text-sm"><i class="fa fa-phone-alt mr-1"></i> Phone Number<b class="d-block">{{ partner.phone }}</b></p>
                        <p class="text-sm"><i class="fa fa-envelope mr-1"></i> Email<b class="d-block">{{ partner.email }}</b></p>
                        <p class="text-sm"><i class="fa fa-calendar-alt mr-1"></i> Joined<b class="d-block">{{ dateFrom(partner.created_at) }}</b></p>
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
            partner: {},
            products: [],
            product: {},
            reviews: [],
            transaction_count: 0,
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods:{
        addTransaction(){
            this.loading = true;
            this.editMode = false;
            this.product = {};
            $('#productModal').modal('show');
            this.loading = false;  
        },
        closeModals(){
            $('#productModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/escrows/partners/'+this.$route.params.id)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Partner loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Partner not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.partner = response.data.partner;
            this.products = response.data.products;
            this.reviews = response.data.reviews;
            this.transaction_count = response.data.transaction_count;
            this.closeModals();
        },
    },
}
</script>