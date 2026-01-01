<template>
<div class="card">
    <div class="card-header bg-dark">
        <h3 class="card-title">Soon To Expire</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped text-nowrap">
            <thead class="bg-navy">
                <tr>
                    <th>Item</th>
                    <th>Batch ID</th>
                    <th>Expiry Date</th>
                    <th>Quantity</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-if="store_items.total != 0 && store_items != null && store_items.total != null">
                <tr v-for="store_item in store_items.data">
                    <td>{{ store_item.item.name }}</td>
                    <td>{{ store_item.batch.batch_number }}</td>
                    <td>{{ store_item.batch.expiry_date }}</td>
                    <td>{{ store_item.balance }}</td>
                    <td></td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="5">No Item is about to Expire</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer" v-if="view != 'dashboard'">
        <pagination v-model="current_page" @paginate="getInitials" :per-page="store_items.per_page != null ? store_items.per_page : 52" :records="store_items.total != null ? store_items.total : 550" ></pagination>
    </div>
</div>
</template>
<script>
export default {
    data(){
        return  {
            current_page: 1,
            editMode: false,
            form: new Form({}),
            loading: false,
            store_item: {},
            store_items: {},
        }
    },
    mounted() {
        //this.getInitials();
    },
    methods:{
        addStore(){
            this.loading = true;
            this.editMode = false;
            $('#storeModal').modal('show');  
            this.loading = false;
        },
        closeModals(){
            $('#storeModal').modal('hide');
        },
        deleteStore(id){
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
                if(result.value){
                    this.form.delete('/api/inventory/store_items/'+id)
                    .then(response=>{
                        Fire.$emit('storeReload', response);  
                        Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials(page=1){
            this.loading = true;
            axios.get('/api/inventory/store_items?specific=ste&page='+page)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Stores loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Stores not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.stores = response.data.stores;
            this.closeModals();
        },
        updateStore(store){
            this.loading = true;
            this.editMode = true;
            this.store = store;
            $('#storeModal').modal('show');
            this.loading = false;         
        },
    },
    props:{
        store_id: Number,
        view: String
    },
    watch:{
        store_id(){
            this.getInitials(this.store_id);
        }
    }
}
</script>