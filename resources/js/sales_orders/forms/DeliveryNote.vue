<template>
<section class="overlay-wrapper">    
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row p-0 m-0">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Deliver To:</h3>
                </div>
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle" :src="'/dist/img/user4-128x128.jpg'" alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center">{{  order.customer != null ? order.customer.name: '' }}</h3>
                    <p class="text-muted text-center"  v-html="order.customer != null ? order.customer.address: ''"></p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Address</b> <span class="float-right" v-html="order.customer != null ? order.customer.address: ''"></span>
                        </li>
                        <li class="list-group-item">
                            <b>Email</b> <span class="float-right" v-html="order.customer != null ? order.customer.email: ''"></span>
                        </li>
                        <li class="list-group-item">
                            <b>Phone</b> <span class="float-right" v-html="order.customer != null ? order.customer.phone: ''"></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Items</h3>
                </div>
                <div class="card-body table-responsive p-0"> 
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Package Type</th>
                                <th>Package Qty</th>
                                <th>Total Qty</th>
                                <th>Delivered Qty</th>
                                <th>To Deliver Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in order.order_items" :key="index">
                                <td>{{ item.item.name }}</td>
                                <td>{{ item.quantity }}</td>
                                <td>{{ item.package.name }}</td>
                                <td>{{ item.package_quantity }}</td>
                                <td>{{ item.total_quantity}} units</td>
                                <td>{{ item.delivered_quantity ?? 0 }} units</td>
                                <td v-if="item.delivered_quantity < item.total_quantity"><input type="number" class="form-control" v-model="deliveryNoteData.order_items[index].delivery_quantity" /> units</td>
                                <td v-else>0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <button class="btn btn-primary" @click.prevent="createDeliveryNote()">Create Delivery Note</button>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            items: [],
            loading: false,
            deliveryNoteData: new Form({
                order_id: '',
                order_items: [],
            }),
            //order_items: [],
        }
    },
    emits:['deliveryReload'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        addBatch(index){
            this.deliveryNoteData.items[index].batches.push({batch_number: '', quantity: 0, expiry_date: '',});
        },
        createDeliveryNote(){
            this.loading = true;
            this.deliveryNoteData.order_id = this.order.id;
            this.deliveryNoteData.post('/api/sales/delivery_notes')
            .then(response =>{
                this.$emit('deliveryReload');
                this.$swal.fire({icon: 'success', title: 'The Delivery Note Created Has Been Confirmed', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = false;
        },
    },
    props: {
        order: Object,
    },
    watch:{
        order(){
            this.deliveryNoteData.fill(this.order);
        },
    }
}
</script>