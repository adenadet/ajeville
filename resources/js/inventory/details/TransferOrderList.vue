<template>
    <section class="p-0 overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Unique ID</th>
                    <th v-if="view != 'sales'">Requesting Store</th>
                    <th v-if="view == 'sales'">Visit ID</th>
                    <th v-if="view == 'sales'">Patient</th>
                    <th>Issuing Store</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody v-if="transfer_orders.length > 0">
                <tr v-for="(transfer_order, index) in transfer_orders" :key="transfer_order.id">
                    <td>{{ addOne(index) }}</td>
                    <td>{{ capitalize(transfer_order.unique_id) }} <br /><span class="text-muted">{{transfer_order.name }}</span></td>
                    <td v-if="view != 'sales'">{{ transfer_order.requesting_store != null ? transfer_order.requesting_store.name : 'Requesting Store Not Selected' }}</td>
                    <td v-if="view == 'sales'">{{ transfer_order.visit != null ? transfer_order.visit.unique_id : 'Requesting Store Not Selected' }}</td>
                    <td v-if="view == 'sales'">{{ transfer_order.patient != null ? PatientFullName(transfer_order.patient): 'Requesting Store Not Selected' }}</td>
                    <td>{{ transfer_order.issuing_store != null ? transfer_order.issuing_store.name : 'Issuing Store Not Selected' }}</td>
                    <td>{{ ExcelDate(transfer_order.created_at) }}</td>
                    <td>{{ transfer_order.status == 0 ? 'Draft' : (transfer_order.status == 1 ? 'Awaiting Authorization' :(transfer_order.status == 2 ? 'Unaccepted' : (transfer_order.status == 3 ? 'Awaiting Issuance' :(transfer_order.status > 3 && transfer_order.status < 6 ? 'Ongoing' : (transfer_order.status == 6 ? 'Completed' : 'Rejected')))))  }}</td>
                    <td>
                        <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v text-dark"></i></button>
                        <div class="dropdown-menu">
                            <router-link class="btn btn-block dropdown-item" :to="'/inventory/transfer_orders/'+transfer_order.id"><i class="fa fa-eye mr-1"></i> View </router-link>
                            <button class="btn btn-block dropdown-item" @click="rejectTransferOrder(transfer_order.id)"><i class="fa fa-trash mr-1 text-danger"></i> Cancel Request</button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td :colspan="view != 'sales' ? 7 :8"><p>No Request has been made</p></td>
                </tr>
            </tbody>
        </table>
    </section>
</template>
<script>
export default {
    data() {
        return {
            available_stores: [],
            types: [],
            categories: [],
            editMode: false,
            form: new Form({}),
            loading: false,
            my_stores: [],
            transfer_order: {},
            types: [],
        }
    },
    mounted() {},
    methods: {
        addItem(item){
            const { length } = this.TransferData.items;
            const id = length + 1;
            const found = this.TransferData.items.some(el => el.id === item.id);
            if (!found) this.TransferData.items.push({ id: item.id, name: item.name, quantity: 1});
            else{
                const index = this.TransferData.items.findIndex(object => {
                    return object.id === item.id;
                });

                this.TransferData.items[index].quantity++; 
            }
        },
        cancelOrder(id) {
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                if (result.value) {
                    this.form.delete('/api/inventory/transfer_orders/' + id)
                        .then(response => {
                            this.$emit('storeReload', response);
                            this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                        })
                        .catch(() => {
                            this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                        });
                }
            });
        },
        closeModals() {
            $('#storeModal').modal('hide');
        },
        rejectTransferOrder(id) {
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel it!'
            })
            .then((result) => {
                if (result.value) {
                    this.form.delete('/api/inventory/transfer_orders/' + id)
                    .then(response => {
                        this.$emit('storeReload', response);
                        this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                    });
                }
            });
        },
        itemPop(item){
            const index = this.TransferData.items.findIndex(object => {
                return object.id === item.id;
            });
            this.TransferData.items.splice(index, 1)
        },
        refreshPage(response) {
            this.available_stores = response.data.stores;
            this.categories = response.data.categories;
            this.my_stores = response.data.my_stores;
            this.types = response.data.types;
            this.closeModals();
        },
        searchItems(){
            this.ItemData.post('/api/inventory/items/search')
            .then(response =>{
                this.search_results = response.data.search_results;
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
            });
        },
    },
    props:{
        transfer_orders:Array,
        view: String,
    },
    watch:{
        transfer_orders(){
            this.loading = true;
            this.loading = false;
        }
    }
}
</script>