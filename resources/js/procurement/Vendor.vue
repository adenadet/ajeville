<template>
<section class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-navy">
                <h3 class="card-title">About {{ vendor.name }}</h3>
            </div>
            <div class="card-body">
                <strong><i class="fas fa-book mr-1"></i> Category</strong>
                <p class="text-muted">{{ vendor.category != null ? vendor.category.name : 'None' }}</p>
                <hr>
                <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>
                <p class="text-muted" v-html="vendor.address"></p>
                <hr>
                <strong><i class="fas fa-globe mr-1"></i> Website</strong>
                <p class="text-muted" v-html="vendor.website"></p>
                <hr>
                <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                <p class="text-muted" v-html="vendor.email"></p>
                <hr>
                <strong><i class="fas fa-phone-alt mr-1"></i> Phone Number</strong>
                <p class="text-muted" v-html="vendor.phone"></p>
                <hr>
                <strong><i class="fas fa-map-marker-alt mr-1"></i> Tax Identification Number</strong>
                <p class="text-muted" v-html="vendor.tin"></p>
                <hr>
                <strong><i class="fas fa-map-marker-alt mr-1"></i> Withholding Tax</strong>
                <p class="text-muted" v-html="vendor.withholding_tax"></p>
                <hr>
                <strong><i class="fas fa-map-marker-alt mr-1"></i> VAT</strong>
                <p class="text-muted" v-html="vendor.vatable ? 'Active' : 'Inactive'"></p>
                <hr>
                <strong><i class="far fa-file-alt mr-1"></i> Details</strong>
                <p class="text-muted" v-html="vendor.description"></p>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <ProcurementDetailVendorContactList :vendor.sync="vendor" />
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
            search: '',
            type: 'all',
            vendor: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        addVendor(){
            this.loading = true;
            this.editMode = false;
            this.vendor = {};
            //Fire.$emit('StoreDataFill', {});
            $('#vendorFormModal').modal('show');  
            this.loading = false;
        },
        closeModals(){
            $('#vendorFormModal').modal('hide');
            $('#vendorModal').modal('hide');
        },
        deleteVendor(id){
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
                        Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        getInitials(page=1){
            this.closeModals();
            this.loading = true;
            axios.get('/api/procurement/vendors/'+this.$route.params.id)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Vendors loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Vendors not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.vendor = response.data.vendor;
        },
        updateVendor(vendor){
            this.loading = true;
            this.editMode = true;
            this.vendor = vendor;
            $('#vendorFormModal').modal('show');
            this.loading = false;         
        },
    },
}
</script>
