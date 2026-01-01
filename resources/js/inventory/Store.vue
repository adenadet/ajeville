<template>
<section class="row">
    <div class="col-md-4">
        <InventoryDetailStoreSummary :store.sync="store" />
    </div>
    <div class="col-md-8">
        <InventoryDetailStoreTransferRequest :store_id="$route.params.id" type="outgoing" specific="pending" view="dashboard"/>
        <InventoryDetailStoreTransferRequest type="incoming" specific="pending"  view="dashboard"/>
        <InventoryDetailStoreSoonToExpire :store_id="$route.params.id" view="dashboard"/>
        <InventoryDetailStoreExpired :store_id="$route.params.id" view="dashboard"/>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            current_page: 1,
            editMode: false,
            form: new Form({}),
            loading: false,
            store: {},
        }
    },
    mounted() {
        this.getInitials();
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
                    this.form.delete('/api/inventory/stores/'+id)
                    .then(response=>{
                        Fire.$emit('storeReload', response);  
                        this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    })
                    .catch(()=>{
                    this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/inventory/stores/'+this.$route.params.id)
            .then(response =>{
                this.refreshPage(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Stores loaded successfully',
                });
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Stores not loaded successfully',
                })
            });
            this.loading = false;
                
        },
        refreshPage(response){
            this.store = response.data.store;
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
}
</script>