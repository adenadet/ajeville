<template>
<section class="container-fluid">
    <form @submit.prevent="editMode ? updateSO() :createSO()">
        <div class="card">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-navy">
                                <h3 class="card-title">Sales Order Details</h3>
                            </div>
                            <div class="card-body pb-0">
                                <div class="row">
                                    <div v-if="form_type!='direct_sales'" class="col-md-12">
                                        <div class="form-group">
                                            <label>Name:</label>
                                            <input v-if="!editMode && transferData.status <= 1" type="text" class="form-control" id="name" name="name" v-model="transferData.name" required>
                                            <div v-else class="form-control" v-html="transferData.name" required></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Issuing Store:</label>
                                            <select v-if="transferData.issuing_store_id == null || (!editMode && transferData.status<=1)" class="form-control" id="issuing_store_id" name="issuing_store_id" v-model="transferData.issuing_store_id" required>
                                                <option value="">--Select Store</option>
                                                <option v-for="store in my_stores" :value="store.id">{{ store.name }}</option>
                                            </select>
                                            <div v-else class="form-control" v-html="transfer_order.issuing_store != null ? transfer_order.issuing_store.name: transfer_order.issuing_store_id"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group" >
                                            <label>Patient Type:</label>
                                            <select class="form-control" id="patient_type_id" name="patient_type_id" v-model="transferData.patient_type_id"  required>
                                                <option value="">--Select Patient Type--</option>
                                                <option value="walk-in">Walk In Patient</option>
                                                <option value="active_visit">Active Visit</option>
                                                <option value="new_visit">Registered Patient (New Visit)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Patient:</label>
                                            <select v-if="!editMode && transferData.patient_type_id == 'active_visit'" class="form-control" id="visit_id" name="visit_id" v-model="transferData.visit_id"  required>
                                                <option value="">--Select Patient--</option>
                                                <option v-for="visit in visits" :value="visit.id">{{ patientName(visit.patient) }}</option>
                                            </select>
                                            <model-list-select v-else-if="!editMode && transferData.patient_type_id == 'new_visit'" class="form-control" :list="patients" v-model="patient_id" option-value="unique_id" :custom-text="codeAndNameAndDesc" placeholder="Select Applicant" />
                                            <div v-else-if="!editMode && transferData.patient_type_id == 'walk-in'" class="form-control">
                                                <input type="hidden" />
                                                Walk In Patient
                                            </div>
                                            <div v-else class="form-control" v-html="transfer_order.requesting_store != null ? transfer_order.requesting_store.name: transfer_order.requesting_store_id"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
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
                                            <th width="40%">Item</th>
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
            <div class="card-footer">
                <div class="text-right">
                    <button type="submit" class="btn btn-sm bg-primary"><i class="fas fa-cash-register"></i> Send for Payment</button>
                    <!--button type="button" class="btn btn-sm btn-primary"><i class="fas fa-user"></i> View Profile</button-->
                </div>
            </div>
        </div>
    </form>  
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
                items: [],
                name: '',
                issuing_store_id : '',
                patient_type_id: 0,
                patient_id: 0,
                visit_id: 0,
                status: 0,
                unique_id: '', 
            }),
            patients: [],
            testMode: false,
            types: [],
            visits: [], 
        }
    },
    emits:['transferOrderReload'],
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addLineItem(){
            this.transferData.items.push({ item_id: '', name: '', requested_quantity: 0})
        },
        createSO(){
            this.loading = true;
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
                    this.transferData.post('/api/inventory/sales_orders')
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
                }
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
            axios.get('/api/inventory/sales_orders/initials')
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
            this.items = response.data.items;
            this.my_stores = response.data.my_stores;
            this.patients = response.data.patients;
            this.visits = response.data.visits;
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
        updateSO(){
            this.loading = true;
            this.transferData.put('/api/inventory/sales_orders/'+this.transferData.id)
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