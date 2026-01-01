<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form>
        <alert-error :form="BranchPriceListData"></alert-error> 
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Price List </label>
                    <select class="form-control" id="price_list_id" name="price_list_id" v-model="BranchPriceListData.price_list_id">
                        <option value="">--Select Price List --</option>
                        <option v-for="price_list in price_lists" :value="price_list.id">{{ price_list.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Branch</label>
                    <select v-if="main_branch == null" class="form-control" id="branch_id" name="branch_id" v-model="BranchPriceListData.branch_id">
                        <option value="">--Select Branch --</option>
                        <option v-for="branch in branches" :value="branch.id">{{ branch.name }}</option>
                    </select>
                    <div v-else v-html="main_branch.name" class="form-control">
                    </div>
                </div>
            </div>
            <div class="col-md-12" v-if="source == 'emr'">
                <div class="form-group">
                    <label>Plan</label>
                    <select class="form-control" id="plan_id" name="plan_id" v-model="BranchPriceListData.plan_id">
                        <option value="">--Select Plan--</option>
                        <option v-for="plan in plans" :value="plan.id">{{ plan.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="BranchPriceListData.status">
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
            BranchPriceListData: new Form({
                description: '',
                id: '',
                branch_id: '', 
                plan_id: '',
                price_list_id: '',
                status: '',
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
            this.BranchPriceListData.branch_id = this.main_branch != null ? this.main_branch.id : this.BranchPriceListData.branch_id;
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
            this.branches = response.data.branches;
            this.plans = response.data.plans;
            this.price_lists = response.data.price_lists;
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
        branch_price_list: Object,        
        editMode: Boolean,
        main_branch: Object,
        source: String,
    },
    watch:{
        branch_price_list(){
            this.BranchPriceListData.fill(this.branch_price_list);
        }
    }
}
</script>