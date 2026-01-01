<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-tabs">
                    <form>
                        <div class="card-header p-0 pt-1">
                            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="initials-tab" data-toggle="pill" href="#initials" role="tab" aria-controls="initials" aria-selected="true">Initialize</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="items-tab" data-toggle="pill" href="#items" role="tab" aria-controls="items" aria-selected="false">Items</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-0">
                            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                            <div class="tab-content" id="custom-tabs-one-tabContent">
                                <div class="tab-pane fade show active" id="initials" role="tabpanel" aria-labelledby="initials-tab" href="#initials">
                                    <div class="col-md-8 offset-md-2">
                                        <div class="row">
                                            <div v-if="form_type!='direct_sales'" class="col-md-12">
                                                <div class="form-group">
                                                    <label>Name:</label>
                                                    <input v-if="!editMode && transferData.status <= 1" type="text" class="form-control" id="name" name="name" v-model="transferData.name" required>
                                                    <div v-else class="form-control" v-html="transferData.name" required></div>
                                                </div>
                                            </div>
                                            <div :class="form_type=='direct_sales' ? 'col-md-12' : 'col-md-6'">
                                                <div class="form-group">
                                                    <label>Issuing Store:</label>
                                                    <select v-if="transferData.issuing_store_id == null || (!editMode && transferData.status<=1)" class="form-control" id="issuing_store_id" name="issuing_store_id" v-model="transferData.issuing_store_id" required>
                                                        <option value="">--Select Store</option>
                                                        <option v-for="store in available_stores" :value="store.id">{{ store.name }}</option>
                                                    </select>
                                                    <div v-else class="form-control" v-html="transfer_order.issuing_store != null ? transfer_order.issuing_store.name: transfer_order.issuing_store_id"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6" v-if="form_type=='direct_sales'">
                                                <div class="form-group" >
                                                    <label>Patient Type:</label>
                                                    <select class="form-control" id="patient_type_id" name="patient_type_id" v-model="transferData.patient_type_id"  required>
                                                        <option value="">--Select Patient Type--</option>
                                                        <option value="walk-in">Walk In Patient</option>
                                                        <option value="active_visit">Registered Patient (Active Visit)</option>
                                                        <option value="new_visit">Registered Patient (New Visit)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6" v-if="form_type=='direct_sales'">
                                                <div class="form-group" v-if="form_type=='direct_sales'">
                                                    <label>Patient:</label>
                                                    <select v-if="!editMode && transferData.patient_type_id == 'active_visit'" class="form-control" id="requesting_store_id" name="requesting_store_id" v-model="transferData.requesting_store_id"  required>
                                                        <option value="">--Select Patient--</option>
                                                        <option v-for="patient in patients" :value="patient.id">{{ store.name }}</option>
                                                    </select>
                                                    <EMRPatientFormSearch v-else-if="!editMode && transferData.patient_type_id == 'new_visit'" />
                                                    <div v-else-if="!editMode && transferData.patient_type_id == 'walk-in'" class="form-control">
                                                        <input type="hidden" />
                                                        Walk In Patient
                                                    </div>
                                                    <div v-else class="form-control" v-html="transfer_order.requesting_store != null ? transfer_order.requesting_store.name: transfer_order.requesting_store_id"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6" v-else>
                                                <div class="form-group" v-if="form_type!='direct_sales'">
                                                    <label>Requesting Store:</label>
                                                    <select v-if="transferData.issuing_store_id == null || (!editMode && transferData.status<=1)" class="form-control" id="requesting_store_id" name="requesting_store_id" v-model="transferData.requesting_store_id"  required>
                                                        <option value="">--Select Store</option>
                                                        <option v-for="store in my_stores" :value="store.id">{{ store.name }}</option>
                                                    </select>
                                                    <div v-else class="form-control" v-html="transfer_order.requesting_store != null ? transfer_order.requesting_store.name: transfer_order.requesting_store_id"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Description:</label>
                                                    <QuillEditor v-if="!editMode && transferData.status <= 1" content-type="html" theme="snow" rows="5" id="description" name="description" v-model:content="transferData.description" />
                                                    <div v-else v-html="transferData.description" class="border p-1 rounded-2"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade p-0" id="items" role="tabpanel" aria-labelledby="items-tab" href="#items">
                                    <div class="row p-0">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header bg-dark">
                                                    <h3 class="card-title">List of Items</h3>
                                                    <div class="card-tools">
                                                        <button v-if="!editMode" class="btn btn-tool btn-xs" @click="addLineItem()" type="button"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                                <div class="card-body p-0">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 10px">#</th>
                                                                <th>Item</th>
                                                                <th>Requested Quantity</th>
                                                                <th v-if="transfer_order.status >= 1">Approved Quantity</th>
                                                                <th v-if="transfer_order.status >= 2">Issued Quantity</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(item, index) in transferData.items">
                                                                <td>{{ addOne(index) }}</td>
                                                                <td><model-list-select class="form-control" :list="items" v-model="transferData.items[index].item_id" option-value="id" option-text="name" placeholder="Select Item" /></td>
                                                                <td>
                                                                    <div  v-if="transfer_order.status >= 1"  class="form-control"  v-html="transferData.items[index].requested_quantity"></div>
                                                                    <input v-else type="number" class="form-control" v-model="transferData.items[index].requested_quantity"/>
                                                                </td>
                                                                <td v-if="transfer_order.status >= 1">
                                                                    <input v-if="transfer_order.status == 1" type="number" class="form-control" v-model="transferData.items[index].approved_quantity"/>
                                                                    <div v-else class="form-control" v-html="transferData.items[index].approved_quantity"></div>
                                                                </td>
                                                                <td v-if="transfer_order.status >= 2">
                                                                    <input v-if="transfer_order.status == 2" type="number" class="form-control" v-model="transferData.items[index].transfer_quantity"/>
                                                                    <div v-else class="form-control" v-html="transferData.items[index].transfer_quantity"></div>
                                                                </td>
                                                                <td><button class="btn btn-sm btn-danger" @click="itemPop(index)"><i class="fa fa-trash"></i></button></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-grey">
                            <div class="col-12 p-0">
                                <button v-if="transferData.status == 0 || transferData.status == ''" type="submit" class="btn btn-sm bg-teal" @click.prevent="saveTransferOrder()"><i class="far fa-save"></i> Save As Draft</button>
                                <button type="submit" class="btn btn-sm bg-danger" @click.prevent="cancelTransferOrder()"><i class="fa fa-trash"></i> Cancel Request</button>
                                
                                <button v-if="transferData.status == 0" type="submit" class="btn btn-sm btn-primary float-right" @click.prevent="createTransferOrder()"><i class="fas fa-check"></i> Send For Confirmation</button>
                                <button v-if="transferData.status <= 1" type="submit" class="btn btn-sm btn-info float-right" @click.prevent="createAuthTransferOrder()"><i class="fas fa-check-double"></i> {{transfer_order.status == 1 ? 'Authorize' : 'Auto Authorize'}}</button>
                                <button v-if="transferData.status == 2" type="submit" class="btn btn-sm btn-primary float-right" @click.prevent="acceptTransferOrder()"><i class="fas fa-check"></i> Accept Request</button>
                                <!--button v-if="transferData.status <= 4" type="submit" class="btn btn-sm bg-teal float-right" @click.prevent="updateTO()" ><i class="far fa-save"></i> Update</button-->
                                <button v-if="transferData.status == 5" type="submit" class="btn btn-sm bg-teal float-right" @click.prevent="completeTransferOrder()" ><i class="far fa-save"></i> Complete Transfer Order</button>
                            </div> 
                        </div>
                    </form>
                </div>   
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return { 
            available_stores: [],
            items: [],
            loading: false,
            my_stores: [],
            patients_active: [],
            transferData: new Form({
                id: '',
                description: '',
                requesting_store_id: '',
                items: [],
                name: '',
                issuing_store_id : '',
                status: 0,
                unique_id: '', 
            }),
            testMode: false,
            types: [],   
        }
    },
    emits:['transferOrderReload'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        acceptTransferOrder(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This request will require accepted! The total requested quantity will be approved if you do not enter an issue quantity",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, I know!'
            })
            .then((result) => {
                if (result.value) {
                    this.transferData.status = 3;
                    if (this.editMode){this.updateTO()}
                    else{this.createTO()}
                }
            });
        },
        addLineItem(){
            this.transferData.items.push({ item_id: '', name: '', requested_quantity: 0})
        },
        createTO(){
            this.loading = true;
            this.transferData.post('/api/inventory/transfer_orders')
            .then(response =>{
                this.loading = false;
                this.$emit('transferOrderReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Item has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.loading = false;
            });  
        },
        createAuthTransferOrder(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This request will not require Authorization!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, I am authorizing it!'
            })
            .then((result) => {
                if (result.value) {
                    this.transferData.status = 2;
                    //alert(this.testMode ? 'True' : 'False'); return ;
                    if (this.editMode){this.updateTO()}
                    else{this.createTO()}
                }
            });
        },
        createTransferOrder(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "This request will require Authorization!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, I know!'
            })
            .then((result) => {
                if (result.value) {
                    this.transferData.status = 1;
                    if (this.editMode){this.updateTO()}
                    else{this.createTO()}
                }
            });
        },
        getAllInitials() {
            this.loading = true;
            axios.get('/api/inventory/transfer_orders/initials')
            .then(response => {
                this.refreshPage(response);
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'warning',
                    title: 'Transfer Requests not loaded successfully',
                })
            });
        },
        itemPop(item){
            this.transferData.items.splice(index, 1)
        },
        refreshPage(response) {
            this.available_stores = response.data.stores;
            this.categories = response.data.categories;
            this.my_stores = response.data.my_stores;
            this.items = response.data.items;
        },
        saveTransferOrder(){
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
                    this.transferData.status = 0;
                    if (this.editMode){this.updateTO()}
                    else{this.createTO()}
                }
            });
        },
        updateTO(){
            this.loading = true;
            this.transferData.put('/api/inventory/transfer_orders/'+this.transferData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('transferOrderReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Item has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.loading = false;
            });  
        },
    },
    props:{
        editMode: Boolean,
        form_type: String,
        transfer_order: Object,
    },
    watch:{
        transfer_order(){
            this.transferData.reset(); this.transferData.status = 0;
            if (this.transfer_order != null){this.transferData.fill(this.transfer_order)}
        },
        editMode(){
            this.testMode = this.editMode;
            //alert(this.testMode ? 'Something changed' : 'Not Working');
        }
    }
}
</script>