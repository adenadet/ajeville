<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updatepriceListData() : createpriceListData()">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" required class="form-control" id="name" name="name" placeholder="Name *" v-model="priceListData.name" :class="{'is-invalid' : priceListData.errors.has('name') }">
                    <has-error :form="priceListData" field="name"></has-error> 
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Type</label>
                    <select class="form-control" v-model="priceListData.type_name" placeholder="Select Current Status">
                        <option value="cash">Cash</option>
                        <option value="credit">Credit</option>
                        <option value="insurance">Insurance</option>
                    </select>
                    <has-error :form="priceListData" field="type_name"></has-error> 
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" v-model="priceListData.status" placeholder="Select Current Status">
                        <option value=1>Active</option>
                        <option value=0>Inactive</option>
                    </select>
                    <has-error :form="priceListData" field="status"></has-error> 
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor  option-text="name" placeholder="Select Payment Type" />
                    <has-error :form="priceListData" field="bank_id"></has-error> 
                </div>
            </div>
            <button type="submit" name="submit" class="submit btn btn-primary">Submit</button>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            banks: [],
            priceListData: new Form({
                id: '',
                name: '',
                mode_id: '', 
            }),
            loading: false,
            modes: [],
            trans_sum: 1,
        }
    },
    emits:['refreshPriceListForm'],
    mounted() {
        this.getInitials();
    },
    methods:{
        createpriceListData(){
            this.loading = true;
            this.priceListData.post('/api/finance/price_lists')
            .then(response =>{
                this.$emit('refreshPriceListForm', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Pricelist has been created',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });  
            this.loading = false;
        },
        getInitials(){
            axios.get('/api/finance/price_lists/initials')
            .then(response =>{
                //this.nations = response.data.nations;
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Price List Form did not load properly',
                })
            });
        },
        updatepriceListData(){
            this.loading = true;
            this.priceListData.put('/api/finance/price_lists/'+this.priceListData.id)
            .then(response =>{
                this.$emit('refreshPriceListForm', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Price List details has been updated',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = false;            
        },
    },
    props:{
        editMode: Boolean,
        price_list: Object,
    },
    watch:{
        price_list(){
            this.priceListData.fill(this.price_list);
        }
    }
}
</script>
