<template>

</template>
<script>
export default {
    computed:{
        all_vendors(){
            if (this.assignVendorData.category_id != '') {
                return this.vendors.filter(vendor => vendor.category_id === this.assignVendorData.category_id);
            }
    
            else{ return this.vendors}
        }
    },
    data() {
        return {
            categories: [],
            actionData: new Form({
                id: '',
                dispute_id: '',
                user_id: '',
                type_id: '',
                comment: '',
                status_id: '',
            }),
        }
    },
    emits:['refreshPage'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {    
        createAction(){
            this.actionData.put(address)
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Other Cost has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },    
        getAllInitials() {
            this.loading = true;
            axios.get('/api/escrows/dispute_actions/initials')
            .then(response => {
                this.categories = response.data.categories;
                this.vendors = response.data.vendors;
                this.loading = false;
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Vendor Assign Form did not loaded successfully',})
                this.loading = false;
            });
        },
        updateAction(){
            this.loading = true;
            this.actionData.put('/api/escrows/dispute_actions/')
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Other Cost has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        
    },
    props: {
        editMode: Boolean,
        item_type: String,
        item: Object,
    },
    watch:{
        item(){
            if (this.item_type == 'purchase_order'){this.actionData.po_id = this.item.id;}
            else if (this.item_type == 'work_order'){this.actionData.wo_id = this.item.id;}
        },
    }
}
</script>