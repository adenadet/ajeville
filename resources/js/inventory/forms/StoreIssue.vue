<template>
<section class="">
    <form>
        <alert-error :form="itemIssueData"></alert-error> 
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h3 class="card-title">Issue Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Issue Type</label>
                                    <select v-if="issue_type == 0" class="form-control" id="name" name="name" v-model="itemIssueData.issue_type">
                                        <option value="">--Select Issue Type--</option>
                                        <option value="1">Transfer Requests</option>
                                        <option value="2">Patient Issue</option>
                                    </select>
                                    <div v-else class="form-control">
                                        <p v-if="issue_type == 1">Transfer Request</p>
                                        <p v-else-if="issue_type == 2">Patient Issue</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Reference ID</label>
                                    <select v-if="issue_type == 0" class="form-control" id="name" name="name" v-model="itemIssueData.reference_id">
                                        <option value="">--Select Parent Issue--</option>
                                        <option v-for="reference in references" :value="reference.id">{{ reference.unique_id }}</option>
                                    </select>
                                    <input v-else class="form-control" type="text" id="reference_id" name="reference_id" v-model="itemIssueData.reference_id" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card" v-for="(item,index) in fulfillables">
                    <div class="card-header" id="headingOne">
                        <h3 class="card-title text-small">{{ item.item.name }}</h3>
                        <div class="card-tools"><button type="button" class="btn btn-xs btn-primary" @click="addFulfillment(index)"><i class="fa fa-plus mr-1"></i> Fulfill</button></div> 
                    </div>
                    <div class="card-body p-0">
                        <div class="card">
                            <div class="card-body p-0">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Batch ID</th>
                                            <th>Quantity</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="itemIssueData.items[index].fulfillments != null">
                                        <tr v-for="(fulfillment, quest) in itemIssueData.items[index].fulfillments">
                                            <td>{{addOne(quest)}}</td>
                                            <td>
                                                <select class="form-control" v-model="itemIssueData.items[index].fulfillments[quest].batch_id" required>
                                                    <option value="">--Select Batch--</option>
                                                    <option v-for="fulfill in fulfillables" :value="fulfill.id">{{ fulfill.batch.item_id }} [expiry_date]</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" />
                                            </td>
                                            <td>
                                                <button class="btn btn-xs btn-danger" type="button" @click="removeFulfill()"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr><td cols="4">Not Working</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="id" id="id" v-model="itemIssueData.id" />
        <button @click.prevent="editMode ? updateIssue() : createIssue()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            fulfillables: [],
            fulfillment: {
                batch_id: '',
                quantity: '',
            },
            items: [],
            itemIssueData: new Form({
                id: '',
                issue_type: '',
                items:[
                    {
                        id: '',
                        item_id: '',
                        transfer_quantity: '',
                        fulfillments:[],
                    },
                ],
                name: '', 
                parent_category_id: '',
                regerence_id: '',
                status: '',
                unique_id: '',
            }),
        }
    },
    emits: ['reloadIssue'],
    mounted() {
        
    },
    methods:{
        addFulfillment(index){
            alert(index)
            if(this.itemIssueData.items[index].fulfillments == null){
                this.itemIssueData.items[index].fulfillments = [];
                this.itemIssueData.items[index].fulfillments.push(this.fulfillment);
            }
            else{
                this.itemIssueData.items[index].fulfillments.push(this.fulfillment);
            }
        },
        createItemIssue(){
            this.loading = true;
            this.itemIssueData.issue_type = this.issue_type;
            this.itemIssueData.reference_id = this.reference_id;
            this.itemIssueData.post('/api/inventory/issues')
            .then(response =>{
                this.loading = false;
                this.$emit('reloadIssue');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Item Issue has been created',
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
        getAllInitals(){
            this.loading = true;
            axios.get('/api/inventory/issues/initials/?t='+this.issue_type+'&ref_id='+this.issue_request.unique_id)
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Issue not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.fulfillables = response.data.fulfillables;
            this.items = response.data.items;
        },
        updateItemIssue(){
            this.loading = true;
            this.itemIssueData.issue_type = this.issue_type;
            this.itemIssueData.reference_id = this.reference_id;
            this.itemIssueData.put('/api/inventory/categories/'+this.itemIssueData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('reloadIssue');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Issue has been updated',
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
                this.loading = false;
            });              
        },
    },
    props:{
        editMode: Boolean,
        issue_request: Object,
        issue_type: String,
        reference_id: Number,
        store_id: Number,
    },
    watch:{
        issue_request(){
            this.itemIssueData.fill(this.issue_request);
            this.itemIssueData.reference_id = this.issue_request.unique_id;
            if (this.issue_request.unique_id != null && this.issue_type != null && this.issue_request.issuing_store_id != null){this.getAllInitals();}
            //this.itemIssueData.fill(this.category);
        }
    }
}
</script>