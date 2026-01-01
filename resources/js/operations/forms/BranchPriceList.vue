<template>
<section class="overlay-wrapper">
    <form>
        <alert-error :form="BranchPriceListData"></alert-error> 
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Price List </label>
                    <select v-if="branch_id != null" class="form-control" id="name" name="name" v-model="BranchPriceListData.branch_id">
                        <option value="">--Select Branch --</option>
                        <option v-for="branch in branches" :value="branch.id">{{ branch.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Branch</label>
                    <select v-if="main_branch == null" class="form-control" id="name" name="name" v-model="BranchPriceListData.branch_id">
                        <option value="">--Select Branch --</option>
                        <option v-for="branch in branches" :value="branch.id">{{ branch.name }}</option>
                    </select>
                    <div v-else v-html="main_branch.name" class="form-control">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Plan</label>
                    <select class="form-control" id="name" name="name" v-model="BranchPriceListData.plan_id">
                        <option value="">--Select Plan--</option>
                        <option v-for="plan in plans" :value="plan.id">{{ plan.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="name" name="name" v-model="BranchPriceListData.status">
                        <option value="">--Select Type--</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor content-type="html" theme="snow" id="description" name="description" v-model:content="BranchPriceListData.description" />
                </div>
            </div>
        </div>
        <input type="hidden" name="id" id="id" v-model="BranchPriceListData.id" />
        <button @click.prevent="editMode ? updateBranchPriceList() : createBranchPriceList()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            branches: [],
            branch_price_list: {},
            BranchPriceListData: new Form({
                description: '',
                id: '',
                name: '', 
                parent_category_id: '',
                status: '',
                type_id: '',
            }),
            loading: false,
            plans: [],
            price_lists: [],
        }
    },
    emits: ['reloadBranchPriceList'],
    mounted() {
        this.getAllInitials();
    },
    methods:{
        createBranchPriceList(){
            this.loading = true;
            this.BranchPriceListData.post('/api/finance/branch_price_lists')
            .then(response =>{
                this.loading = false;
                this.$emit('reloadBranchPriceList');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The BranchPriceList has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
                this.loading = false;
            });  
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/finance/branch_price_lists/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'BranchPriceList Form not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.categories = response.data.categories;
            this.item_types = response.data.item_types;
        },
        updateBranchPriceList(){
            this.loading = true;
            this.BranchPriceListData.put('/api/finance/branch_price_lists/'+this.BranchPriceListData.id)
            .then(response =>{
                this.$emit('reloadBranchPriceList');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The BranchPriceList has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            }); 
            this.loading = false;                 
        },
    },
    props:{
        editMode: Boolean,
        main_branch: Object,
        price_list: Object,        
    },
    watch:{
        price_list(){
            this.BranchPriceListData.fill(this.price_list);
        }
    }
}
</script>